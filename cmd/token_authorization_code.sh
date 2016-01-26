#!/bin/bash

curl -u testclient:testpass http://localhost:81/oauth/token -d 'grant_type=authorization_code&code='"$1"