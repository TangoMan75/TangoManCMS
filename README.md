TangoManCMS
===========

**TangoManCMS** is a simple symfony 3 content manager and blog engine created on September 21, 2016, 3:33 pm.

Project 100% Heroku compatible availlable here : [http://tangomancms.herokuapp.com](http://tangomancms.herokuapp.com)

Installation
============

Step 1: Download the code base
------------------------------

Open a command console, enter your project directory and execute the following command to download the latest stable version of **TangoManCMS**:
```batch
$ git clone https://github.com/TangoMan75/RepositoryHelper.git .
```

Step 2: Download project dependencies
-------------------------------------

Open a command console, enter your project directory and execute the following command to download the latest stable version of **TangoManCMS** vendors:
```batch
$ composer install
```
This command requires you to have Composer installed globally, as explained in the installation chapter of the Composer documentation.

Step 3: Configuration
---------------------

When asked enter the required informations:

### analytics
Your google analytics tracking id, as explained here : [https://support.google.com/analytics/answer/1008080](https://support.google.com/analytics/answer/1008080)

Your google tracking id should look like this : `UA-XXXXXXXX-X`

### database_url
Your database url, with the following syntax : `mysql://root@localhost/cms`

### hotjar
Your hotjar tracking id, as explained here : [https://docs.hotjar.com/v1.0/docs/hotjar-tracking-code](https://docs.hotjar.com/v1.0/docs/hotjar-tracking-code)

Your hotjar tracking id should be a six digit figure like this `000000`.

### mailer_from
Your contact email address.

### mailer_host
Your mailer ip address. i.e.: `127.0.0.1`

### mailer_password
Your mailer password.

### mailer_transport
Default mailer protocol is SMTP.

### mailer_user
Your mailer username.

### secret
A secret key that's used to generate certain security-related tokens.

### site_author
Your full name.

### site_name
Your website name.

### super_admin_pwd
Your password.

### super_admin_username
Your username.
