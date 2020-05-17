## Overview

send notifications to customers by time interval: 1 month, 3 weeks, 2 weeks, 1 week, 3 days, 1 day

## Benefits 

* Automation of sending notifications to customers.

* Have a log of past actions.

## Getting Started

These instructions will get you a copy of the project up and running on your local machine for development and testing purposes. See deployment for notes on how to deploy the project on a live system.

- Rename the config-template.php file to config.php
- Put the right parameters in the config.php file

### Prerequisites

If you want to use the automation function, you need a Linux OS.

### Installation

* Give 755 to logs folder.

* Put the right parameters in the notify-task.sh file.

* Put the right parameters in the notify-cron.sh file

* Put notify-cron.sh in /etc/cron.d/  

* Check if the PHP is able to send mail.

And check if notifications have been sent.