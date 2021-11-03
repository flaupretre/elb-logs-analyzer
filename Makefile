
include config.mk

#-----------------

AWS_SOURCE = s3://$(AWS_BUCKET)/AWSLogs/$(AWS_ACCOUNT)/elasticloadbalancing/$(AWS_REGION)/$(DATE)

#-----------------

all: getlogs csv

csv:
	gunzip <$(DATA_LOGS)/$(PREFIX)-`echo $(DATE) | tr / -`.log.gz | php src/mk_csv.php $(HOST) $(OUTPUT_DIR)/$(PREFIX)-`echo $(DATE) | tr / -`
	cp data/templates/excel-model-spreadsheet.xlsx $(OUTPUT_DIR)/$(PREFIX)-`echo $(DATE) | tr / -`.xlsx

getlogs:
	rm -rf $(TMP_DIR) && mkdir -p $(TMP_DIR)
	AWS_PROFILE=$(AWS_PROFILE) aws s3 sync $(AWS_SOURCE)/ $(TMP_DIR)
	cat `find $(TMP_DIR) -type f -name '*.log'` | sort | gzip --best >$(DATA_LOGS)/$(PREFIX)-`echo $(DATE) | tr / -`.log.gz
	rm -rf $(TMP_DIR)


