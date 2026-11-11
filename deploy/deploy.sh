#! /bin/bash -e
# shellcheck disable=SC2034

# Source deployment defaults
# shellcheck source=/dev/null
source vendor/drenso/symfony-deployer-bundle/deploy-scripts/defaults.sh

# Override project information
PROJECT_API_ENDPOINT="https://gitlab.utwente.nl/api/v4/projects/living-textbook%2Fliving-textbook"
HAS_GIT_SUBMODULES=true
HAS_LATEX_PODMAN_SERVICE=true
SYSTEMD_MESSENGER_BASENAME_DEFAULT="ltb-messenger"
MESSENGER_QUEUES=(
  "email"
)
ASSET_PATHS=(
  "public/build"
  "public/email"
)

# Run the deployment script
# shellcheck source=/dev/null
source vendor/drenso/symfony-deployer-bundle/deploy-scripts/deploy.sh
