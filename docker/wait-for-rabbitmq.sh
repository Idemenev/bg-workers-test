#!/bin/sh

set -e

host="$1"
shift
cmd="$@"

until curl -i -u guest:guest http://$host:15672/api/whoami -c '\q'; do
  >&2 echo "RabbitMQ is unavailable - sleeping"
  sleep 5
done

>&2 echo "RabbitMQ is up - executing command"
exec $cmd