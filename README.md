This project takes a set of ELB log files and produces a set of CSV files,
ready to import in Excel. I personaly use this to analyze AWS EKS (Kubernetes)
access logs and build traffic charts showing client count, request rate throughout the period, and other statistical data.

Log files are read from stdin.

Before launching any of the 'make' target, you must copy the 'config.mk.dist'
file to 'config.mk' and the configuration to reflect your case. Then, you have several possibilities :

- 'make getlogs' retrieves the logs corresponding to the given date, compresses them, and stores them as a single file.
- 'make run' processes the file corresponding to the given data and produces
3 CSV files: one containing request information, one containing client information, and one containing the timeline.

'clients' are the set of source IP addresses present in the logs. The 'clients'
CSV file contains one line per IP and lists several indicators:

- reqs: how many resquests were issued by this source
- (min_tstamp, max_tstamp): the min and max timestamps (Unix format)
 corresponding to the requests issued to this IP (session duration).
- rate: how many requests were issued by minute by this client during its session.

The 'timeline' is a the set of 1-minute intervals between the min and max times
found in the logs. The CSV file contains one line for each minute og logs. This
line contains :
- 'Etstamp': a float number representing the time in Excel format. Display this field as 'Personalized / jj-mm-yyyy hh:mm' format to display it in excel.
- 'Clients': the count of active clients at this time (the count of different sources having issued at least one request during this interval).
- 'Requests': The count of requests received during this interval
- 'RequestsMA': A moving average (subset=5) of the requests
- 'Rate': The average count of request per client during this interval
- 'RateMA': A moving average (subset=10) of the rates

