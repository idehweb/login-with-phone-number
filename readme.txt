=== Plugin Name ===
Contributors: idehweb, hamid alinia
Requires at least: 3.4
Tested up to: 6.3
Stable tag: 1.5.5
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Tags: Login with phone number, login with mobile number, WooCommerce

== Description ==

Login/register with phone number

NEW FEATURE:
Added country flags to login with phone number form

You can add almost every SMS gateways (if you have) yourself for free
you can change style and appearance of forms

You can use Firebase for sending SMS (10,000 free OTP SMS from Firebase)

For checking docs and getting more help please visit:
[Login with phone number documentations](https://idehweb.com/product/login-with-phone-number-in-wordpress/ "login & register with phone number")

* Login/Register with E-mail

* Wordpress Login Form

* Woocommerce Registration Form

* Woocommerce Login With Phone Number

* Wordpress OTP Login

* Woocommerce Registration With Phone Number

* Add Phone Number to Wordpress Registration

* Simple Use

* Support of International SMS Delivery

* Activating Users by Phone Number

* Password Recovery Form

* Page Authentication in Order to Visit Pages

* Login and Registration with Phone Number

* Redirect Users to Specific URLs After Logging in or Registering

AJAX

MODAL

[youtube https://www.youtube.com/watch?v=0B0sE9JMzCE]

== Installation ==

1. download plugin from wordpress directory
1. Upload the ‘login-with-phone-number’ folder to the /wp-content/plugins/ directory
1. Activate it through the ‘Plugins’ menu in WordPress
1. use  [idehweb_lwp] shortcode in your posts and pages where you need user to be logged in
1. use  [idehweb_lwp_metas nicename="false" username="false" phone_number="true" email="false"] where you want to show logged in users metas. for example you can use this shortcode in user's profile page. you can show phone number, email, username and nicename.
1. for sending otp sms, you need credit. you can buy credit inside plugin and use our default gateway, or you can use your custom gateways. some gateways have been added.

== Changelog ==

= 1.5.5=
* fix fatal error

= 1.5.4=
* add modular structure in whole plugin (for developers)
* handle multiple gateways at a time, user can choose OTP gateway on authentication
* enhance UX in admin
* enhance front validations

= 1.5.3=
* introduce new plugin "WhatsApp gateway"
* add filter `lwp_add_to_default_gateways`
* add link of term & conditions, separate it from the text
* add logo inside login/register form (pro version)
* add france translation

= 1.5.2=
* Iranian users can use plugin more convenient
* now we support adding gateways in plugin structure

= 1.4.9=
* esc json , remove bugs

= 1.4.8=
* remove bugs
* add billing phone of woocommerce

= 1.4.7=
Thanks to @marshallthomas47 (at git) who was sponsor of this update (I think)
and @monagjr who have done some good changes:
* Replace the custom-built phone input UI with well-tested and production-ready UI from International Telephone Input
* Option for Terms Default Check Status
* Default Country Settings
* empty placholder shows valid phone example
* show error msg for invalid form before submitting

= 1.4.63=
check all echos for security issues

= 1.4.62=
esc ajax outputs

= 1.4.61=
esc outputs

= 1.4.6=
esc and sanitize inputs, set text domain in string

= 1.4.3=
remove security bugs

= 1.4.2=
tested with wordpress 6.1.2

= 1.4.1=
add login message for logged-in users

= 1.4.0=
remove bugs of registered users

= 1.3.9=
you can add your custom sms gateway yourself

= 1.3.7=
security bug: delete.php file deleted
add sanitize to inputs
change user experience of admin part
change form bugs
better and faster support added!

= 1.3.6=
code sending twice problem solved

= 1.3.5=
add users registered date sortable
remove bugs

= 1.3.4 =
remove bugs for number less than 11 digits
add support for ajax template

= 1.3.3 =
fix class name sticky > lw-sticky

= 1.3.2 =
add documentations in readme

= 1.3.1 =
fix bugs

= 1.3.0 =
add timer for sending sms again
fix bugs of email: code entered wrong
add text localization, ability to change text of labels, fields, errors and...

= 1.2.23 =
remove default option idehweb_use_custom_gateway

= 1.2.22 =
enable option of only login and not register users for network and multi site
add turkish language

= 1.2.21 =
enable option of only login and not register users

= 1.2.20 =
fix bugs of saving styles

= 1.2.19 =
remove firebase jQuery bug
remove support option
add change style settings page

= 1.2.18 =
add Woocommerce form auto change
set Firebase to default

= 1.2.17 =
remove bugs

= 1.2.16 =
remove smsbharti gateway :-( :-x :-|

= 1.2.15 =
remove raygansms gateway

= 1.2.14 =
fix bug of user id in js

= 1.2.13 =
fix bug of auth for normal method
remove some comments

= 1.2.12 =
update mshastra and fix bugs
add firebase for sending OTP sms (10,000 otp free sms)
add firebase config docs

= 1.2.11 =
updating and supporting pt_BR language by Rodriggo Enzo

= 1.2.10 =
add mshastra sms gateway for Arabian users and specially for my friend Hussam Ismail
updating and supporting Arabic language by Hussam Ismail

= 1.2.09 =
fix bugs of smsbharti gateway, not reading sender id
remove default gateway if custom gateway is activated


= 1.2.08 =
fix bugs of smsbharti gateway

= 1.2.07 =
add missed file

= 1.2.06 =
fix bug style of admin
added smsbharti gateway for Indian users
one file missed, this version will crush your site, do not install!


= 1.2.05 =
fix bugs

= 1.2.04 =
add raygansms.com gateway
fix bugs ;) (require classes)

= 1.2.03 =
update zenziva gateway configs
update infobip gateway configs


= 1.2.02 =
fix bug "The REST API route definition is missing the required permission_callback argument"
add new shortcode [idehweb_lwp_metas nicename="false" username="false" phone_number="true" email="false"]
use phone number as username and nicename
remove configuring... loader
add custom gateways => Twilio , Zenziva , Infobip
add default country code


= 1.2.01 =
remove www from domain
remove "domain:" word
remove action change

= 1.2.0 =
add Woocommerce billing_phone phone number update support
remove admin authentication with phone number
add admin authentication with domain name

= 1.1.22 =
update languages
add German / Deutsch language

= 1.1.21 =
add default nickname


= 1.1.20 =
optimize style
optimize admin


= 1.1.17 =
you can set default username

= 1.1.16 =
remove error  Trying to access array offset on value of type bool on line 78

= 1.1.15 =
search input for countries in admin
update frontend performance

= 1.1.14 =
optimize style
add language to header

= 1.1.13 =
change server
increase server stability

= 1.1.12 =
remove 0 from first of phone number

= 1.1.11 =
update readme

= 1.1.10 =
add en_GB language
add ar language

= 1.1.09 =
text domain updated

= 1.1.07 =
update readme installation part2

= 1.1.06 =
update readme installation part

= 1.1.05 =
better support

= 1.1.04 =
country code optimize

= 1.1.03 =
chat and support updated

= 1.1.01 =
languages updated

= 1.1.01 =
add tutorial and guid

= 1.1.0 =
enable sticky position style


= 1.0.9 =
stable version

= 1.0.8 =
login with password
add more countries 

= 1.0.1 =
login with email
add persian translation
add redirect link

= 1.0 =
Initial release

