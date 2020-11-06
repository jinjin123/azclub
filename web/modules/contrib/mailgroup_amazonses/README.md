# Introduction
This module provides an Amazon SES connection plugin for Mail Group. In addition
to SES, S3, and SNS are used to store and notify the module about new messages.

## Requirements
This module requires the AWS Secrets Manager module.

## Installation
Install as you would normally install a contributed Drupal module. Visit
https://www.drupal.org/node/1897420 for further information.

## Configuration
To configure the Amazon services, follow these steps:

* Enable this module and AWS Secrets Manager.
* Create a SNS topic that will be notified when new mail is received.
* Add an HTTP or HTTPS subscription to the SNS topic.
* Create SES rule set.
* Configure AWS Secrets Manager.

#### Create a SNS topic
Create a SNS topic that will be notified when new mail from SES when new mail
is received. You'll add this topic when configuring the rule set.

#### Subscribing to the SNS topic
After creating a SNS topic, you'll need to add a subscription that will notify
the module when new mail is received. The subscription should use either HTTP
or HTTPS protocol. The Endpoint path is /mailgroup/amazonses/receive. For
example, https://example.com/mailgroup/amazonses/receive.

#### S3 Bucket
You can use an existing S3 bucket, or add a new one when when creating a
incoming mail rule set. If using an existing bucket, ensure that SES has
permission to write to it.

#### SES Rule Set
SES rules sets are used to handle incoming mail. Create or edit a rule set with
a recipient of your group's email address. Select the S3 action. Choose an
existing bucket, or create a new one. Select the SNS topic you subscribed to.

#### AWS Secrets Manager
Configure the AWS Secrets Manager module at Configuration > System > AWS Secrets
Manager. Enter credentials that have access to read and delete from the S3
bucket.

## Maintainers
* Ben Davis (davisben) - https://www.drupal.org/u/davisben
