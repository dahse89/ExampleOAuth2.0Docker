#!/bin/bash

# test token route
curl -u testclient:testpass http://localhost:81/oauth/token -d 'grant_type=client_credentials'

