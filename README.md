## Requisites

### SDKs

The PayPal and Amazon AWS SDKs are required for this project, and can be downloaded through composer. composer.phar is included in this repo (but may not always be up to date).

To download dependencies, use `php composer.phar update && php composer.phar install`.


### Backup scripts

There are two backup scripts in the `scripts/` directory: `dbbackup.sh` and `filebackup.sh` (the filenames should be self-explanatory).

`dbbackup.sh` is a script to backup the database using mysqldump and zip it, then create a symbolic link to `backups/db/db-latest-dump.zip` for download on the admin page.
**`dbbackup.sh` should be run daily as a CRON job (0 0 * * *)**

`filebackup.sh` is a script to backup all website files (excluding .git/, vendor/, cgi-bin/ and backups/) in a zip, then create a symbolic link to `backups/files/files-dump-latest.zip` for download on the admin page.
**`filebackup.sh` should be run weekly as a CRON job (0 0 * * 0)**
