<?php
#
# Args:
#   - $1: Host to filter out ('' = no filter)
#   - $2: Result base path
#
# Input comes from stding.
# Input format: AWS ELB logs
#============================================================================

require('Browser/src/Browser.php');
require('RequestSet.php');
require('Request.php');
require('ClientSet.php');
require('Client.php');
require('Timeline.php');
require('Interval.php');
require('MovingAverage.php');
require('Svc.php');
require('SvcSet.php');
require('APICmd.php');

#---

define('SPLIT_LINES', 0);

#---

if ($_SERVER['argc'] < 3) {
  fwrite(STDERR, "Usage: <cmd> <host name> <result base>\n");
  exit(1);
}

$filter_host=$_SERVER['argv'][1];
$result_base=$_SERVER['argv'][2];

echo "Building request set...\n";

$reqs = new RequestSet($filter_host);
$reqs->insert_from_log_stdin();

$start=0;
$index=0;
while(true) {
file_put_contents($result_base.'-Requests'.($index ? "-$index" : "").'.csv'
  , $reqs->csv($start, SPLIT_LINES));
if ($start == 0) break;
$index++;
}

file_put_contents($result_base.'-Clients.csv', $reqs->client_set->csv());

file_put_contents($result_base.'-Svc.csv', $reqs->svc_set->csv());

echo "Building time line...\n";

$tline = new Timeline($reqs);
file_put_contents($result_base.'-Timeline.csv', $tline->csv());


?>
