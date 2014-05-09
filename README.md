## Requisites

### SDKs

The PayPal and Amazon AWS SDKs are required for this project, and can be downloaded through composer. composer.phar is included in this repo (but may not always be up to date).

To download dependencies, use `php composer.phar update && php composer.phar install`.


### AWS Keys

The `lib/form/UploadHandlerS3.php` and `lib/edit-gallery.php` scripts require AWS keys in order to modify files in AWS S3 buckets. The keys to access such buckets are not stored in the document root, for security reasons. They should be stored in `awskeys.php` one level above the document root (i.e. `../awskeys.php`).

The `awskeys.php` should contain the following class:

    :::php
    class AwsKeys {
      public function getAwsKeys() {
        return array (
          'key'    => 'yourKey',
          'secret' => 'yourSecret'
        );
      }
    }


### Backup scripts

There are two backup scripts in the `scripts/` directory: `dbbackup.sh` and `filebackup.sh` (the filenames should be self-explanatory).

#### dbbackup.sh

`dbbackup.sh` is a script to backup the database using mysqldump and zip it, then create a symbolic link to `backups/db/db-latest-dump.zip` for download on the admin page.
**`dbbackup.sh` should be run daily as a CRON job (0 0 * * *)**

#### filebackup.sh

`filebackup.sh` is a script to backup all website files (excluding .git/, vendor/, cgi-bin/ and backups/) in a zip, then create a symbolic link to `backups/files/files-dump-latest.zip` for download on the admin page.
**`filebackup.sh` should be run weekly as a CRON job (0 0 * * 0)**

#### AWS S3

Both backup scripts are set to automatically sync the backups to S3 (in the SDC-backups bucket). This (obviously) requires an S3 account, and uses Amazons [AWS CLI](https://aws.amazon.com/cli/) which must be configured beforehand.
If the `aws` command is not found, then the scripts will not attempt to upload them to S3.
