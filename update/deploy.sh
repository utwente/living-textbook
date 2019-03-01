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

# Pull the new data
ssh-agent bash -c 'ssh-add .ssh-token; git pull'

# Replace vendors/assets with new files
paths=(
  "public/build"
)
for i in "${paths[@]}"; do
    mv "${i}" "${DEPLOY_DIR}/${i}.old"
    mv "${DEPLOY_DIR}/${i}" "${i}"
done

# Install vendors (without scripts)
composer install -o --apcu-autoloader --no-dev --no-scripts

# Clear cache
sudo -u www-data php bin/console cache:clear

# Run install vendors again but now with scripts, effectively only executing the scripts
composer install -o --apcu-autoloader --no-dev

# Execute migrations
sudo -u www-data php bin/console doctrine:migrations:migrate -n

# Restore frontend controller
cp update/controllers/index.php public/index.php

# Remove deployment artifacts
rm -rf ${DEPLOY_DIR}
