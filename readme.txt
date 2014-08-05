=== PG FIXED BOX ===

Features :
- ability to show a complete sidebar in a popup box.
- two different effects for box it self and its button !
- ability to make unlimited number of boxes !
- ability to show in specific pages of your site
- ability to make the box shows automatically or by user click or both !
- ability to make a box invisibile without deleting it !
- ability to change the box width and height and button width and height !
- ability to change the the button and box background color directly from settings page
- ability to add custome css in every box settings !

Contributors: ParsiGroup.net Team

Donate link: 

Requires at least: wordpress 3.5 +

Tags: pop up sidebar, fixed boxes 

Tested up to: wordpress 3.9

Stable tag: trunk

License: "GPLv2 or later"

== Description ==
This is a pop up sidebars plugin , which means first you create a box from plugin page in wordpress
dashboard, after that plugin creates a widgetarea in Appearance > widgets , then you go there and
insert few widgets in that widget area , and you are done ! just visit your site , page or any place
that you specified in box settings page and enjoy!

NOTE : widget that you insert in the box needs to be styled !
you can add style for your widgets in your theme style.css file or inserting them in plugin style file
or adding them to every box custome css textarea

== Installation ==
HOW TO INSTALL
-extratc plugin files
-copy folder pg-fixed-box into wordpress plugins folder which is located in : yourdomain/wp-content/plugins
or after downloading the plugin go to wordpress dashboard and navigate to plugins > installed plugins , then from top click Add New button , in next page press upload , select the file and press Install Now.

HOW TO ACTIVATE
-go to wordpress dashboard and navigate to plugins > installed plugins , from tab All or Inactive find the "PG Fixed Boxes WP Plugin (pop up sidebars plugin)" and below the title press Activate.
-wait until wordpress shows a box containing plugin activated

HOW TO USE
-after activating the plugin it will automatically creates a box with default information
-for viewing current available boxes navigate to : Settings > Fixed Boxes
-for adding new box navigate to : Settings > Add New Box
-after editing box settings go to Dashboard > Appearance > Widgets and insert widgets in the widget area that has same name as your box
-then make sure you checked  Show this box in my site  in your box settings.
-now visit your site and you should see the widget in there !

== Frequently Asked Questions ==
Q : can i change the speed and of effects ?
A : yes you can and it is easy ! go to plugin js folder open the file effects.js and modify numbers within function function runboxeffect and function runbtneffect

Q : where is the plugin style file ?
A : it is in the plugin css folder and its name is styles.css

Q : how i can change the button position?
A : first choose one direction for button in boxes settings page ( which name is Fixed Boxes) 
and then in example choose top left after that open the file styles.css in : pg-fixed-box > css > styles.css ,
in that file find the selector ".pg-btn-tl" < in the selector name 'tl' means top left , now edit the numbers within the selector braces " {} "

Q : where i can use this plugin ?
A : as it is a wordpress plugin , everywhere you can install wordpress , you can use this plugin too !

Q : im disabled the blugin but sidebar contents are not removed how to remove them ?
A : for prevent the data lost wordpress keeps your widgets even if that widget area is not there
if you dont want that widgets just drag them to the top of inactive sidebars area and refresh the page!
