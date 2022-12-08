# wp_zapper
WordPress custom Plugin Demo. Focus on extending WP_List_Table class. This version of the plugin will be used to administer custom user groups.

## Installation:
Download and **install** the plugin, just as you would any WordPress plugin via **.zip upload** from your WordPress **Dashboard**.

*Run* the included SQL file to **seed some data** to actually see what it does. I recommend just copy paste the few lines of SQL into a running instance of ./adminer.php or /phpMyAdmin. you can put it on top of the data in your existing db. it shouldn't hurt anything. It's just 10 users which were created by FakerPress (i think? it's been so long ago!). It adds the benign Users, which each have email, privs, etc so I was able to test, and the addl table fields are wp_usermeta.

### The SQL Included. Check your plugin folder. 
[![this is what it adds](https://i.imgur.com/hfqQXlF.png "the data that will be put in your wp_usermeta table")]
