
# Bulk Price Update
## Authors

- [Plugins & Snippets](https://pluginsandsnippets.com/)


## Features

- Seprate License Page to develop premium plugin
- Seprate Setting Page
- Multi Language or Translation Support
- Deactivation Popup
- Installed Plugin Page action links
- Promotion Section - Pending
- MailChimp News Letter - Pending
- Plugin Review Notice on Top of the Admin pages


## Documentation - Start New Plugin using this Template

Follow this below steps to change branding.

1) Search for Plugin Name: *"Bulk Price Update"* and replace it.
2) Search for Plugin Text Domain: *"bulk-price-update"* and replace it.
3) Search for Plugin Main Class: *"Bulk_Price_Update"* and replace it.
4) Search for Plugin Prefix: *"bpu"* with your but must start with *"ps_"*.
5) Search for Plugin Prefix: *"ps-pt"* with your but must start with *"ps-"*.
6) Rename Plugin main file
7) Double-check the dependencies in the main file
8) Update all constants name & values
9) If Plugin not require database table then remove load_table() from the main plugin file otherwise update accordingly.
10) Update Action link on Wordpress Installed Plugin using bpu_action_links() in main plugin file.
11) Update pages names in bpu_show_license_message() to show License notice like License Expired or Inactive, on Plugin pages only instead of everywhere in wp admin.

## To Start Free Plugin

To start developing free plugin does not require following code/blocks. So please remove it to make it clean code.
1) License Page/file and its Method from the main plugin file
2) Remove License related contants from the main plugin file
3) Remove EDD_SL_Plugin_Updater Class file and its calling
## License

[MIT](https://choosealicense.com/licenses/mit/)

