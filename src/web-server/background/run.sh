#!/bin/bash

# This is seriously stupid, but I can't get the environment variables
# or the cron job to run successfully without passing the variables
# over to the correct cron user's environment

# Run our background crons every minute
echo "#!/bin/bash" > /root/background-cron.sh;
echo "" >> /root/background-cron.sh;
echo "export PROGRAM_ENVIRONMENT=\"$PROGRAM_ENVIRONMENT\"" >> /root/background-cron.sh;
echo "export AWS_ACCESS_KEY_ID=\"$AWS_ACCESS_KEY_ID\"" >> /root/background-cron.sh;
echo "export AWS_SECRET_ACCESS_KEY=\"$AWS_SECRET_ACCESS_KEY\"" >> /root/background-cron.sh;
echo "export TENKPIZZA_DB_HOST=\"$TENKPIZZA_DB_HOST\"" >> /root/background-cron.sh;
echo "export TENKPIZZA_DB_NAME=\"$TENKPIZZA_DB_NAME\"" >> /root/background-cron.sh;
echo "export TENKPIZZA_DB_USER=\"$TENKPIZZA_DB_USER\"" >> /root/background-cron.sh;
echo "export TENKPIZZA_DB_PASS=\"$TENKPIZZA_DB_PASS\"" >> /root/background-cron.sh;
echo "" >> /root/background-cron.sh;
echo "/usr/local/bin/php /var/www/src/web-server/background/runAlertCheck.php >> /root/run_alert.log;" >> /root/background-cron.sh;
echo "" >> /root/background-cron.sh;

# Start logging
rsyslogd

# Start the cronjobs
cron -L15

# Run Apache
/usr/sbin/apache2ctl -D FOREGROUND
