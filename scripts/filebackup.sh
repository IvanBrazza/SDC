#!/bin/bash
#
# A script to backup all site files for Star Dream Cakes.
# The latest backup is stored at ../backups/files/files-dump-latest.zip via a symbolic link.
# The script should be run via CRON weekly (0 0 * * 0).
#

# Set some vars
TIMESTAMP="$(date -u +%Y%m%d-%H%M%S)"
LATEST="$(date -u +%c)"
BACKUP_DIR="/home/ivanrsfr/sdc_backups"
FILES_BACKUP_DIR="$BACKUP_DIR/files"
FILES_BACKUP_NAME="files-dump-$TIMESTAMP"

# Make sure directories exist
if [ ! -d "$BACKUP_DIR" ]; then
  echo "$BACKUP_DIR not found, creating...";
  mkdir $BACKUP_DIR;
fi
if [ ! -d "$FILES_BACKUP_DIR" ]; then
  echo "$FILES_BACKUP_DIR not found, creating...";
  mkdir $FILES_BACKUP_DIR;
fi

# Start file backup
echo "Backing up files...";
zip -r $FILES_BACKUP_DIR/$FILES_BACKUP_NAME.zip ../ -x "../.git/*" "../vendor/*" "../cgi-bin/*" # Zip files
if [ -L "$FILES_BACKUP_DIR/files-dump-latest.zip" ]; then # If symbolic link exists
  rm $FILES_BACKUP_DIR/files-dump-latest.zip # Delete old symbolic link
fi
ln -s $FILES_BACKUP_NAME.zip $FILES_BACKUP_DIR/files-dump-latest.zip # Create new symbolic link
echo $LATEST > $FILES_BACKUP_DIR/latest.txt # Update latest backup time
type -P aws &>/dev/null && AWS="true" || AWS="false" # Check if AWS CLI is present
if [ "$AWS" = "true" ]; then
  aws s3 sync $FILES_BACKUP_DIR/ s3://SDC-backups/files/ --delete # Sync to S3
fi
