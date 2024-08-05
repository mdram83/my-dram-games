#!/bin/bash
# This script will adjust the permissions on the environment file and create the systemd service file for the WebSocket server

# Adjust permissions so the webapp user can read the environment file
sudo chmod 644 /opt/elasticbeanstalk/deployment/env

cat <<EOF | sudo tee /etc/systemd/system/websockets.service
[Unit]
Description=Laravel WebSockets Server
After=network.target

[Service]
ExecStart=/bin/bash -c '\''while read -r line; do export "\$line"; done < /opt/elasticbeanstalk/deployment/env && /usr/bin/php /var/app/current/application/artisan websockets:serve'\''
Restart=always
User=webapp
Group=webapp
Environment=HOME=/var/app/current

[Install]
WantedBy=multi-user.target
EOF

sudo systemctl daemon-reload
sudo systemctl enable websockets.service
sudo systemctl start websockets.service