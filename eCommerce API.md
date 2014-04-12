### eCommerce API Description:

Payment process consists of two request types: **check** and **pay**.

Request are send using GET method

On the **check** step payment parameters are being checked. A request is sent to the web interface specified in the project settings in the dashboard to verify parameters provided for the payment.

On the **pay** step payment confirmation comes in. A request is sent to the web interface specified in the project settings in the dashboard to confirm that payment has been received.


#### List of parameters present in every request:


<table>
<tr>
<td>Parameter name</td>	<td>Description</td>
</tr><tr>
<td>command</td>	<td>Request type (check/pay)</td>
</tr><tr>
<td>account</td>	<td>Payment ID (main identification parameter)</td>
</tr><tr>
<td>qxt_[param_name]</td>	<td>Additional identification parameters specified in the project settings. These paramemters get tranfered with <b>qxt_</b> prefix. All additional parameters, added in the project settings are being sent.</td>
</tr><tr>
<td>sign</td>	<td>md5 signature</td>
</tr><tr>
</table>


<br />
#### Forming an md5 signature string: <br />
**[command][sorted_params][secret_key]**<br />
**[command]** – value of command paremeter<br />
**[secret_key]** – secret key specified in the project settings,<br />
**[sorted_params]**  -  string consisting of all the parameter values in the request except **sign**, **command** and **test** (if test parameter was sent) in alphabetic order of **their names**.<br />


For example secret key is being set to: **hd1827** and following parameters are present in the request:
<table>
<tr>
<td>Parameter name</td>	<td>Value</td>
</tr><tr>
<td>command</td>	<td>check</td>
</tr><tr>
<td>account</td>	<td>user_login</td>
</tr><tr>
<td>qxt_server</td>	<td>server</td>
</tr><tr>
<td>qxt_group</td>	<td>vip</td>
</tr><tr>
<td>sign</td>	<td>e579c5c8a73221eece608f6f70d12998</td>
</tr><tr>
</table>



In this case generated md5 string will look like:
**checkuser_loginvipserverhd1827**

where **check** – is the value of command parameter, 
**user_login**, **vip**, **server** – are values of all the parameters in alphabetic order of their names (**account**, **qxt_group**, **qxt_server**)
and **hd1827** is a secret key of the project

 
#### Check Request Description: 


#### Request format: 
Lets assume that web interface on the projects side is located at: 
http://ecommerce-domain.com/handler.php
and following additional identification parameters are being used : **server** and **group**

#### Request example:


http://ecommerce-domain.com/handler.php?command=check&account=[account]&qxt_server=[qxt_server]&qxt_group=[qxt_group]&sign=e579c5c8a73221eece608f6f70d12998&test=1

#### Request parameters description:


<table>
<tr>
<td>Parameter name</td>	<td>Description</td>
</tr><tr>
<td>command</td>	<td>Request type (check/pay)</td>
</tr><tr>
<td>account</td>	<td>Payment ID (main identification parameter)</td>
</tr><tr>
<td>qxt_[param_name]</td>	<td>Additional payment identification parameters</td>
</tr><tr>
<td>sign</td>	<td>md5 signature</td>
</tr><tr>
<td>test</td>	<td>Parameter marking transaction as a test transaction</td>
</tr>
</table>



#### Web interface on the sever side of the project should generate following answer in xml:

> <?xml version="1.0" encoding="UTF-8"?><br />
> &lt;response&gt;<br />
> &nbsp;&nbsp;&nbsp;&nbsp;&lt;result&gt;[result]&lt;/result&gt;<br />
> &nbsp;&nbsp;&nbsp;&nbsp;&lt;comment&gt;[comment]&lt;/comment&gt;<br />
> &lt;/response&gt;<br />



#### Web interface answer description:

<table>
<tr>
<td>Parameter name</td>	<td>Value</td>	<td>Required</td>
</tr><tr>
<td>result</td>	<td>Specified in the table below</td>	<td>Yes</td>
</tr><tr>
<td>comment</td>	<td>Comment</td>	<td>Optional</td>
</tr><tr>
</table>

#### result parameter values:

<table>
<tr>
<td>Value</td>	<td>Description</td>
</tr><tr>
<td>0</td>	<td>Payment identification parameters are correct. Payment can be processed.</td>
</tr><tr>
<td>2</td>	<td>Payment identification parameters are incorrect</td>
</tr><tr>
<td>3</td>	<td>Invalid md5</td>
</tr><tr>
<td>7</td>	<td>Payment with specified identification parameters cannot be done for technical reasons</td>
</tr><tr>
</table>


#### Pay Request Description:


#### Request format: 
Lets assume that web interface on the projects side is located at: 
http://ecommerce-domain.com/handler.php
and following additional identification parameters are being used : **server** and **group**

#### Request example:
http://ecommerce-domain.com/handler.php?command=pay&account=[account]&qxt_server=[qxt_server]&qxt_group=[qxt_group]&sum=[sum]&user_payed=[user_payed]&pay_system_id=[pay_system_id]&game_sum=[game_sum]&currency_id=[currency_id]&price=[price]&rate=[rate]&user_fee=[user_fee]&fee=[fee]&game_count=[game_count]&client_sum=[client_sum]&sign=d9db3650cb33e3fdae0efa696cffc9f2

<table>
<tr>
<td>Parameter name</td>	<td>Description</td>	<td>Value</td>	<td>Always present</td>
</tr><tr>
<td>command</td>	<td>Request type (check/pay)</td>	<td>pay</td>	<td>Yes</td>
</tr><tr>
<td>account</td>	<td>Payment ID (main identification parameter)</td>	<td></td>	<td>Yes</td>
</tr><tr>
<td>qxt_[param_name]</td>	<td>Additional payment identification parameters</td> <td></td>		<td>If no additional parameters are specified in project settings, no qxt_ parameters are sent</td>
</tr><tr>
<td>id</td>	<td>Transaction ID</td>	<td>Integer</td>	<td>Yes</td>
</tr><tr>
<td>merchant_id</td>	<td>Merchant transaction ID</td> 	<td>Integer</td>	<td>Is only sent in repeating request. Is not sent in initial request.</td>
</tr><tr>
<td>sum</td>	<td>Payment sum to be converted into amount of product</td>	<td>Float</td>	<td>Yes</td>
</tr><tr>
<td>user_fee</td>	<td>Transaction fee paid by user</td>	<td>Float</td>	<td>Yes</td>
</tr><tr>
<td>client_sum</td>	<td>Payment sum for the payout</td>	<td>Float</td>	<td>Yes</td>
</tr><tr>
<td>fee</td>	<td>Transaction fee paid by project</td>	<td>Float</td>	<td>Yes</td>
</tr><tr>
<td>user_payed</td>	<td>Payment sum paid by user</td>	<td>Float</td>	<td>Yes</td>
</tr><tr>
<td>pay_system_id</td>	<td>ID of payment method used</td>	<td>Float</td>	<td>Yes</td>
</tr><tr>
<td>price</td>	<td>Price of the product unit on the moment of invoice creation</td>	<td>Float</td>	<td>Yes</td>
</tr><tr>
<td>currency_id</td>	<td>Payment currency ID</td>	<td>Float</td>	<td>Yes</td>
</tr><tr>
<td>rate</td>	<td>Exchange rate to the price of the product</td>	<td>Float</td>	<td>Yes</td>
</tr><tr>
<td>product_amount</td>	<td>Number of product units to be transferred to user</td>	<td>Float</td>	<td>Yes</td>
</tr><tr>
<td>date</td>	<td>Date and time of payment</td>	<td>YYYY-MM-DD HH:MM:SS</td>	<td>Yes</td>
</tr><tr>
<td>sign</td>	<td>md5 signature</td>	<td>md5 hash</td>	<td>Yes</td>
</tr><tr>
<td>test</td>	<td>Transaction marked as a test transaction</td>	<td>0/1</td>	<td>Only transferred in test transactions. If parameter test is present user should not be getting product</td>
</tr><tr>
</table>




#### Web interface on the sever side of the project should generate following answer in xml:
> <?xml version="1.0" encoding="UTF-8"?><br />
> &lt;response&gt;<br />
> &nbsp;&nbsp;&nbsp;&nbsp;&lt;id&gt;[id]&lt;/id&gt;<br />
> &nbsp;&nbsp;&nbsp;&nbsp;&lt;merchant_id&gt;[merchant_id]&lt;/merchant_id&gt;<br />
> &nbsp;&nbsp;&nbsp;&nbsp;&lt;sum&gt;[sum]&lt;/sum&gt;<br />
> &nbsp;&nbsp;&nbsp;&nbsp;&lt;result&gt;[result]&lt;/result&gt;<br />
> &nbsp;&nbsp;&nbsp;&nbsp;&lt;comment&gt;[comment]&lt;/comment&gt;<br />
> &lt;/response&gt;<br />


#### Web interface answer description


<table>
<tr>
<td>Parameter name</td>	<td>Description</td>	<td>Value</td>	<td>Required</td>
</tr><tr>
<td>id</td>	<td>Transaction ID</td>	<td>Integer</td>	<td>Yes</td>
</tr><tr>
<td>merchant_id</td>	<td>Merchant transaction ID</td>	<td>Integer</td>	<td>Yes</td>
</tr><tr>
<td>sum</td>	<td>Number of product units transeffed to user</td>	<td>Float</td>	<td>Yes</td>
</tr><tr>
<td>result</td>	<td>Result</td>	<td>See table of possibe result below</td>	<td>Yes</td>
</tr><tr>
<td>comment</td>	<td>Comment</td>	<td></td>	<td>No</td>
</tr><tr>
</table>




#### !!!	
Please note that all parameters except comment are required. Even in case of Error, answer with all required parameters has to be sent. 0 value should be sent if valid results cannot be sent due to the Error. If some of the required parameters are not sent or sent with blank values the answer won’t be accepted as valid.

There could be more that one payment notification request for the same transaction. First one should be processed and for all of the following ones the same response as for the first one should be sent.


#### Result parameter values:


<table>
<tr>
<td>Value</td>	<td>Description</td>	<td>Fatal</td>
</tr><tr>
<td>0</td>	<td>Success</td>	<td></td>
</tr><tr>
<td>1</td>	<td>Temporary error, please try again later</td>	<td>No</td>
</tr><tr>
<td>2</td>	<td>Payment identification parameters are incorrect</td> <td>Yes</td>
</tr><tr>
<td>3</td>	<td>Invalid MD5</td>	<td>Yes</td>
</tr><tr>
<td>4</td>	<td>Invalid request (invalid sum, all or some of required parameters are not present)</td>	<td>Yes</td>
</tr><tr>
<td>5</td>	<td>Other error</td>	<td>Yes</td>
</tr><tr>
<td>7</td>	<td>Payment with specified identification parameters cannot be done for technical reasons</td>	<td>Yes</td>
</tr><tr>
</table>

#### Timeouts
«Timeout» error is associated with payment if no response is sent whithin 7 seconds.

If no fatal error occurred 3 repeating requests will be sent with interval of 10 minutes.
One more request will be sent in 30 minutes, one after 60 minutes and 2 more in 180 minutes interval. If no answer comes after that, “no answer” fatal error is associated with payment. 

Repeating requests are sent with the same values as the first one with merchant_id parameter present.

#### Important notice

Please be aware that at any moment new parameters can be transferred in API notifications. Make sure that your payment handler is ready accept more parameters than specified in this documentation. Along with adding new parameters documentation will be updated.

