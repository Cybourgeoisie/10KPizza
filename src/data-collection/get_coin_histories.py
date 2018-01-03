# sudo easy_install -U requests
import requests
import json
import boto3

# Get the coin list
url = 'https://api.coinmarketcap.com/v1/ticker/'
response = requests.get(url)

# For successful API call, response code will be 200 (OK)
coins = []
if (response.ok):
	json_data = json.loads(response.content)
	for record in json_data:
		coins.append(record['symbol'])

# For each coin, get histograms
for coin_id in coins:

	# Turn ahead
	if (coin_id == 'MIOTA'):
		coin_id = 'IOT'

	url = 'https://min-api.cryptocompare.com/data/histoday?fsym=' + coin_id + '&tsym=USD&limit=90'
	response = requests.get(url)

	# Turn back
	if (coin_id == 'IOT'):
		coin_id = 'MIOTA'

	# For successful API call, response code will be 200 (OK)
	if (response.ok):
		# Open the file for writing
		filename = 'data/' + coin_id + '_3mo.json'
		f = open(filename, 'w')

		json_data = json.loads(response.content)
		data = json_data['Data']

		f.write('{"data":[')
		first_row = True
		first_ts = 0
		last_ts = 0
		for row in data:
			if not first_row:
				line = ','
				last_ts = row['time']
			else:
				line = ''
				first_row = False
				first_ts = row['time']
			line += '{"close":' + str(row['close']) + ',"time":' + str(row['time']) + '}'
			f.write(line)
		f.write('],"time_start":' + str(first_ts) + ',"time_end":' + str(last_ts) + '}\r\n')

		f.close()

		# Let's use Amazon S3
		s3 = boto3.resource('s3')

		# Upload a new file
		data = open(filename, 'rb')
		s3.Bucket('www.10k.pizza').put_object(Key='data/' + filename, Body=data, ACL='public-read')
