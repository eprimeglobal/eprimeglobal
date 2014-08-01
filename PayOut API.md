## Pay Out API Description:

You should add such headers to every request to our API:

	API-Version: 1.0
	Content-type: application/json
	API-Key: {{Your API key, which we'll give you upon a request}}

### Transfer Request

<b>POST</b> request to url: [http://pwall.pmnthndlr.com/api/masspayments](http://pwall.pmnthndlr.com/api/masspayments)

At the request body you should send structure like this: (json formatted)


	{
		pay_system_id: {{Desired pay system id}},
		withdrawals: [
			{
				transaction_id: "{{unique transfer id at your system}}",
				account: "{{desired pay system user account id (login, email, etc)}}",
				amount: {{amount in desired pay system currency, floating delimiter is dot}}
			}
		]
	}


for example, to transfer 1.42 USD for paypal user anton@eprimeglobal.com with transaction id "dj918hdhda",
<br />2.43 USD for paypal user error@nowhere.com with transaction id "jd1o0sjk" (assuming, that this transfer will cause an error) and
<br />3.31 USD for payopal user same@address.com with transaction id "t77ujhas" (assuming, that this transfer was made later)
<br />you will have to provide us such structure:

	{
		pay_system_id: 113,
		withdrawals: [
			{
				transaction_id: "dj918hdhda",
				account: "anton@eprimeglobal.com",
				amount: 1.42
			},
			{
				transaction_id: "jd1o0sjk",
				account: "error@nowhere.com",
				amount: 2.43
			},
			{
				transaction_id: "t77ujhas",
				account: "same@address.com",
				amount: 3.31
			}
		]
	}

### Transfer Response
Response example:

	{
		status: "Success",
		withdrawalResults: [
			{
				account: "anton@eprimeglobal.com",
				amount: 1.42,
				transaction_id: "dj918hdhda",
				result: "Success”
			},
			{
				account: "error@nowhere.com",
				amount: 2.43,
				transaction_id: "jd1o0sjk",
				result: "Success"
			},
			{
				account: "same@address.com",
				amount: 0,
				transaction_id: "t77ujhas",
				result: "Pay out with the same transaction_id was requested earlier. Current status is: 'Success'”
			},
		]
	}

Response description:

"status" field can take such values: "Success", "Failed" or "error".
"error" means, that is was an error accessing our API (non well-formed request, invalid api-key, etc).
In this case, detailed error description will be at "error_text" field. Also, response http status in this case will differ from “200 OK”.

Two other values ("Success" or "Failed") indicates if the request to desired pay system was successful.

If the desired pay system declined request, then "status" value will be "Failed" and "error_text" field and "error_code" field will be filled with error description and error code respectively.

If the desired pay system accepted our request, then "status" field will have "Success" value.

!!!! It does not mean that all users will definitely receive their money. It only means, that pay system accepted the request and will process it later.

If the request was accepted, then our response will have "withdrawalResults" array .

This array contains of all your money recipients and every element has "account", "amount" и "result" fields.
If any recipient was declined by pay system, or if we it was not send to pay system, then "amount" field value will be zero and "result" field will have an explanation, why this recepient was not processed or not sent. Otherwise, the "result" field will have "Success" value.

Once again draw you attention that request acceptance does not mean that recipient will receive money. That's why "result" field of "error@nowhere.com" user is "Success". We will be receiving updated from pay system in some time (usually less that a minute) after submitting transfer request.

### Transaction Status Request
You can request the result status of transfer by making "GET" request to [https://pwall.pmnthndlr.com/api/masspayment/{{id}}/{{account}}](https://pwall.pmnthndlr.com/api/masspayment/{{id}}/{{account}})

### Transaction Status Response
Response will be same like this:

	{"status":"success","transaction_status":"Declined"}

"status" field can take "Success" and "Failed" values and indicates, if the request to our API was successful
"transaction_status" field can take "Success", "Declined" and "Pending" values, which respectively meant that "transaction was successful (user got his money)", "transaction was declined" and "we are still waiting for transaction status update from pay system"

Draw your attention, that string fields check ("success", "failed" and so on) should be case-insensitive
