=== 2StepAuth ===
Contributors: rajvid9
Tags: 2step, authentication, security, login, sms, verification, user authentication
Requires at least: 2.0.2
Tested up to: 3.3.2
Stable tag: 1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

2StepAuth increases security of Wordpress blogs by adding 2nd level of Authentication. Keeps your blog secure from hackers.

== Description ==

2StepAuth increases security of Wordpress blogs by adding 2nd level of Authentication. After entering correct login credentials, the user has to validate himself using one of 3 ways: SMS Verification, Backup Codes or Email Verification to gain access to his/her blog.

The user has to simply complete 3 step procedure to enable 2nd level of Authentication for Wordpress blog.

== Screenshots ==

1. 2StepAuth Admin Panel accessible via Settings->2StepAuth. Make sure you complete Step 3 for proper working of 2StepAuth

2. 2nd Level of Authentication using SMS Verification. After entering right login credentials, you will be asked to enter a valid code (sent to your registered mobile phone) inorder to get access to admin panel.

3. 2nd Level of Authentication using Backup Codes. After entering right login credentials, you have to enter a valid Backup code to get access to the admin panel.

4. 2nd Level of Authentication using Email Verification. You have to enter a valid code (sent to your registered Email address) inorder to get access to the admin panel.

== Installation ==

How to get it working:

1. Upload `2StepAuth` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to Settings->2StepAuth.
4. Complete the 3 Steps (Enter Email, validate Phone and Generate Backup Codes) and 2StepAuth is ready to protect your blog.
5. The next time you login to your Admin panel, you will find 2nd level of Authentication enabled.


== Frequently Asked Questions ==

= How does 2StepAuth increase security of my blog? =
2StepAuth adds second level of Authentication (a.k.a 2nd layer of security) which helps in preventing unauthorized access to your blog Admin panel. The hacker, inorder to gain access to your blog admin panel, has to complete 2nd level of Authentication by using one of the 3 methods: SMS Verification, Email Verification or Backup codes. Thus, even if your password is compromised, the Wordpress blog is safe.

= What are the basic requirements for working of 2StepAuth? =
1. Wordpress (so obvious!!!) 
2. TextMagic or SMSGlobal account (in case of non-Indian bloggers). This requirement is optional.

= What if I dont have TextMagic or SMSGlobal Account? =
If you dont have TextMagic or SMSGlobal account, SMS Verification feature won't be enabled. The plugin will work with Backup Codes and Email Verification feature.

= I have completed Step 2. Why 2StepAuth is not working? =

2StepAuth depends mainly on completion of Step 3. So, you have to complete Step 3 for working of 2StepAuth.

= I am unable to complete Step 2. What should I do? =

You can skip it and proceed to Step 3. Make sure you generate and save Backup Codes from Step 3.

= What type of users are supported? =
2StepAuth supports users with 'Administrator' privilege. Other users won't feel the presence of 2StepAuth and they will be redirected to Dashboard on correct login.

= Does 2StepAuth supports multiple users(Administrators)? = 
Yes, 2StepAuth recognizes different Administrators and supports blogs with multiple Admins.