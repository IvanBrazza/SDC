#!/bin/bash
#
# A script to backup the MySQL database (using mysqldump) for Star Dream Cakes.
# The latest backup is stored at ../backups/db/db-dump-latest.zip via a symbolic link.
# The script should be run via CRON daily (0 0 * * *).
#

# Set some vars
TIMESTAMP="$(date -u +%Y%m%d-%H%M%S)"
LATEST="$(date -u +%c)"
BACKUP_DIR="/home/ivanrsfr/sdc_backups"
DB_BACKUP_DIR="$BACKUP_DIR/db"
DB_BACKUP_NAME="db-dump-$TIMESTAMP"

# Make sure directories exist
if [ ! -d "$BACKUP_DIR" ]; then
  echo "$BACKUP_DIR not found, creating...";
  mkdir $BACKUP_DIR;
fi
if [ ! -d "$DB_BACKUP_DIR" ]; then
  echo "$DB_BACKUP_DIR not found, creating...";
  mkdir $DB_BACKUP_DIR;
fi

# Start database backup
echo "Backing up database...";
mysqldump -u ivanrsfr -pinspiron1520 ivanrsfr_sdc > $DB_BACKUP_NAME.sql; # Dump the DB
zip temp.zip $DB_BACKUP_NAME.sql; # Zip the .sql dump
rm $DB_BACKUP_NAME.sql; # Remove the temp .sql
mv temp.zip $DB_BACKUP_DIR/$DB_BACKUP_NAME.zip # Move zip to correct location
if [ -L "$DB_BACKUP_DIR/db-dump-latest.zip" ]; then # If symbolic link exists
  rm $DB_BACKUP_DIR/db-dump-latest.zip # Delete old symbolic link
fi
ln -s $DB_BACKUP_NAME.zip $DB_BACKUP_DIR/db-dump-latest.zip # Create new symbolic link
echo $LATEST > $DB_BACKUP_DIR/latest.txt # Update latest backup time
type -P aws &>/dev/null && AWS="true" || AWS="false" # Check if AWS CLI is present
if [ "$AWS" = "true" ]; then
  aws s3 sync $DB_BACKUP_DIR/ s3://SDC-backups/db/ --delete # Sync to S3
fi
