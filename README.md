Opencart Integration Kit for the [Kyash Payment Gateway](http://www.kyash.com/). This extension is OCMod based, hence your core files are not directly modified. Download the lastest extension [here](https://secure.kyash.com/static/sdk/merchant/opencart_kyash-1.0.ocmod.zip)

## Installation
1. Login to Opencart Admin.
2. Go to ```Extensions```->```Extension Installer```.
3. Upload the [opencart_kyash.ocmod.zip](https://secure.kyash.com/static/sdk/merchant/opencart_kyash-1.0.ocmod.zip) file.
4. Go to ```Extensions```->```Modifications```.
5. Hit *Refresh* on the top right corner.
6. Go to ```Extensions```->```Payments```.
7. You should see *Kyash* as one of the options there.
8. Click *Install*.

## Configuration
1. Login to your Kyash Account.
2. Go to Settings.
3. Login to your Opencart Admin.
4. Go to ```Extensions```->```Payments```.
5. Click *Edit*.
6. Enter the credentials listed on your Kyash Account Settings. There are two types of credentials you can enter:
   * To test the system, use the *Developer* credentials. 
   * To make the system live and accept your customer payments use the *Production* credentials.
7. Copy the *Callback URL* listed in Opencart Settings and set it in your Kyash Account settings.

## Testing the Integration.
1. Place an order in your Opencart store.
2. Pick *Kyash - Pay at a nearby shop* as the payment option.
3. Note down the *KyashCode* generated for this order.
4. In a live system, the customer will take this KyashCode to a nearby shop and make the payment using cash.
5. But since we are testing, Login to your Kyash Account.
6. Enter the KyashCode in the search box.
7. You should see a ```Mark as Paid``` button there.
8. Clicking this should change the order status from *pending* to *processing* in your Opencart order details page.


## Troubleshooting
### FTP errors while installing the extension.
If you are facing any FTP related errors while uploading and installing this extension, follow the below steps.

1. Temporarily disable FTP in your Store Settings.
2. Install [this extension](http://www.opencart.com/index.php?route=extension/extension/info&extension_id=18892).
3. Refresh the Extension Modifications.
4. Now proceed towards installing the Kyash extension.

### Paid and Expired KyashCodes are not being marked as such in Opencart.
Once you have successfully installed Kyash extension, if your orders are not being marked as paid after payment is done, then follow the below steps.

* Configure the Kyash Extension using your Kyash *Development API Credentials*.
* Create a test order with Kyash as the payment option.
* Note down the KyashCode returned.
* Login to your Kyash account and search for the KyashCode.
* Mark it as Paid.
* Check if the order status changes from "pending" to "processing" in opencart.
* If the status has not changed, then make the following entry in your .htaccess file just after the ```RewriteEngine On``` entry.
```
RewriteCond %{HTTP:Authorization} .+
RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]
```
* Create another test order and mark it as paid to see if the issue is now fixed.


## Support
Contact developers@kyash.com for any issues you might be facing with this Kyash extension or call +91 8050114225.
