#!/bin/bash

# test resource route
curl http://localhost:81/oauth/resource -d 'access_token='"$1"