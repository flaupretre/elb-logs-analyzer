This project takes a set of ELB log files and produces a set of CSV files,
ready to import in Excel. I personaly use this to analyze AWS EKS (Kubernetes)
access logs and build traffic charts showing client count, request rate throughout the period, and other statistical data.

Log files are read from stdin.

## Installing

### Dependencies

#### PHP CLI

This software uses the PHP CLI interpreter.

Depending on your logs size, you will probably need to increase the
'memory_limit' parameter in your 'php.ini' file (I use '1024M').

#### 'Browser' library

This software uses cbschuld's 'Browser.php' software. In order to install it,
just clone its repo in 'src/Browser' :

- git clone git@github.com:cbschuld/Browser.php.git src/Browser
 
### Configuration

Before launching any of the 'make' target, you must copy the 'config.mk.dist'
file to 'config.mk' and configure it to reflect your case.

## Running

Then, you have several possibilities :

- 'make getlogs' retrieves the logs corresponding to the given date, compresses them, and stores them as a single file.
- 'make run' processes the file corresponding to the given data and produces
3 CSV files: one containing request information, one containing client information, and one containing the timeline.
- use the Makefile as an example to build your own commands.

## Outputs

### Requests

One line per request with every usable information

### Clients

'clients' are the set of source IP addresses present in the logs. The 'clients'
CSV file features one line per IP and gives several values:

- reqs: how many requests were issued by this source
- (min_tstamp, max_tstamp): the min and max timestamps (Unix format)
 corresponding to the requests issued by this IP (session duration).
- rate: how many requests were issued by minute by this client during its whole session.

### Timeline

The 'timeline' is the set of 1-minute intervals between the min and max timestamps
found in the logs. The CSV file contains one line for each minute. This
line contains :

- 'Etstamp': a float number representing the time in Excel format. Display this field as 'Personalized / jj-mm-yyyy hh:mm' format to display it in excel.
- 'Clients': the count of active clients at this time.
- 'Requests': The count of requests received during this interval
- 'RequestsMA': A moving average (subset=5) of the requests
- 'Rate': The average count of request per client during this interval
- 'RateMA': A moving average (subset=10) of the rates

### Active client

A client is supposed to be in an 'active' state if it has issued at least one
request during the last 'N' minutes. 'N' is currently set to '3' and can be
modified by setting the 'CLIENT_ACTIVE_MINUTES' constant to an another value
in the 'Timeline.php' file.





