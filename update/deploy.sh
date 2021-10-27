#! /bin/bash -e

DEPLOY_DIR=.deploy
PROJECT_API_ENDPOINT="https://gitlab.drenso.dev/api/v4/projects/extern%2F013-living-textbook"

# Retrieve token from file
PRIVATE_TOKEN=$(cat .deploy-token)
if [[ -z "$PRIVATE_TOKEN" ]]; then
  echo "Private token not found"
  exit 1
fi;

# Retrieve pipeline id from original command
PIPELINE_ID=${SSH_ORIGINAL_COMMAND}
if [[ -z "$PIPELINE_ID" ]]; then
  echo "Pipeline id not supplied"
  exit 1
fi;

# Retrieve the required artifacts
JOB_INFO=$(curl -sS --header "PRIVATE-TOKEN: ${PRIVATE_TOKEN}" "${PROJECT_API_ENDPOINT}/pipelines/${PIPELINE_ID}/jobs")
ASSET_JOB_ID=$(echo "${JOB_INFO}" | jq '.[] | select(.name == "build-assets" and .status == "success" and .artifacts_file != null) | .id')

if [[ -z "$ASSET_JOB_ID" ]]; then
  echo "Required asset artifact not found in pipeline #${PIPELINE_ID}"
  exit 1
fi

rm -rf ${DEPLOY_DIR}
mkdir ${DEPLOY_DIR}

echo "Downloading assets artifact for job #${ASSET_JOB_ID}..."
curl -sS --header "PRIVATE-TOKEN: ${PRIVATE_TOKEN}" "${PROJECT_API_ENDPOINT}/jobs/${ASSET_JOB_ID}/artifacts" > "${DEPLOY_DIR}/assets.zip"

# Unzip artifacts
unzip -q "${DEPLOY_DIR}/assets.zip" -d "${DEPLOY_DIR}"

# Put the website on updating
cp update/controllers/update.php public/index.php

# Stop the messenger services
messenger_services=(
  "email"
)
for service in "${messenger_services[@]}"; do
  echo "Stopping ltb-messenger@${service}"
  sudo systemctl stop "ltb-messenger@${service}"
done

# Pull the new data, initialize submodules as well
git submodule init
git submodule sync
ssh-agent bash -c 'ssh-add .ssh-token && git pull && git submodule update --recursive'

# Set the current git hash in the local env
COMMIT_HASH=$(git rev-parse --short=8 HEAD)
touch -a .env.local # Make sure the file exists
if grep -xqiE "^COMMIT_HASH=[0-9a-z]+$" .env.local; then
    sed -i -E "s/^COMMIT_HASH=([0-9a-z]+)$/COMMIT_HASH=${COMMIT_HASH}/i" .env.local
else
    echo "COMMIT_HASH=${COMMIT_HASH}" >> .env.local
fi

# Replace vendors/assets with new files
paths=(
  "public/build"
  "public/email"
)
for i in "${paths[@]}"; do
    mv "${i}" "${DEPLOY_DIR}/${i}.old"
    mv "${DEPLOY_DIR}/${i}" "${i}"
done

# Install vendors (without scripts)
composer install -o --apcu-autoloader --no-dev --no-scripts

# Rebuild environment file
composer symfony:dump-env

# Clear cache
sudo -u www-data php bin/console cache:clear

# Run install vendors again but now with scripts, effectively only executing the scripts
composer install -o --apcu-autoloader --no-dev

# Execute migrations
sudo -u www-data php bin/console doctrine:migrations:migrate -n

# Install python environment
php bin/console ltb:python:build

# Restore frontend controller
cp update/controllers/index.php public/index.php

# Start the messenger component
for service in "${messenger_services[@]}"; do
  echo "Starting ltb-messenger@${service}"
  sudo systemctl start "ltb-messenger@${service}"
done

# Remove deployment artifacts
rm -rf ${DEPLOY_DIR}
