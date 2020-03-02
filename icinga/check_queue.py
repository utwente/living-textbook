#!/usr/bin/env python3

import MySQLdb
import sys

FAILED_QUEUE = 'failed'

OK = 0
WARNING = 1
CRITICAL = 2
UNKNOWN = 3

try:
    db = MySQLdb.connect(user="monitor", db="sap3")
    c = db.cursor()

    # Determine whether there are any failed messages
    c.execute("""SELECT COUNT(id) AS c FROM _messenger_queue WHERE queue_name = %s""", (FAILED_QUEUE,))
    failed = c.fetchone()[0]
    if failed == 1:
        print(f'CRITICAL - There is one failed queue message!')
        sys.exit(CRITICAL)
    if failed > 1:
        print(f'CRITICAL - There are {failed} failed queue messages!')
        sys.exit(CRITICAL)

    # Test other messages
    c.execute("""SELECT COUNT(id) AS c FROM _messenger_queue WHERE queue_name != %s AND available_at < (NOW() - INTERVAL 10 MINUTE)""", (FAILED_QUEUE,))
    pending = c.fetchone()[0]
    if pending == 1:
        print(f'WARNING - There is one pending queue message!')
        sys.exit(WARNING)
    if pending > 1:
        print(f'CRITICAL - There are {pending} pending queue messages!')
        sys.exit(CRITICAL)

    print("No failed or pending messages")
    sys.exit(OK)

except Exception as ex:
    print(ex)
    sys.exit(CRITICAL)

print("UNKNOWN - Check failed with unknown error")
sys.exit(UNKNOWN)
