import requests
import boto3

# Get the coin list
url = 'https://api.coinmarketcap.com/v1/ticker/'
response = requests.get(url)

# Let's use Amazon S3
s3 = boto3.resource('s3')
s3.Bucket('www.10k.pizza').delete_objects(Delete={'Objects': [{'Key': 'data/coin-market-cap.json'}]})
s3.Bucket('www.10k.pizza').put_object(Key='data/coin-market-cap.json', Body=response.content, ACL='public-read')
