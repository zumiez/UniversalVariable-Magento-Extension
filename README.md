# Magento Extension for Universal Variable

Qubit's Universal Variable streamlines the process of passing values between OpenTag and your pages, whilst future-proofing against updates and changes. We recommend creating the relevant JavaScript object on your page prior to deploying OpenTag's container script. Doing this will assure that all values will be present on the page when the script runs and can be used by libraried OpenTag scripts. You only need to declare the object variables you use. For example, if your pages only have category and no subcategory, just declare the category. Likewise, if you feel the need to extend objects, or feel like renaming them, please do so. However, please take a note of the new variable names, as these are needed to access your scripts in your OpenTag container.

## Example Usage (Coming Soon)

## Universal Variable Specification
Exported JavaScript object under `universal_variable` on all pages follows open standarded universal variable specification. The specificaiton is also available on GitHub:
[http://github.com/QuBitProducts/UniversalVariable](http://github.com/QuBitProducts/UniversalVariable)

## Installation

### Magento Connect

This is the recommanded way of installing the extenion. Get your extension key on [the extension page](http://www.magentocommerce.com/magento-connect/catalog/product/view/id/13932/s/qubit-universal-variable-9450/) and install the extension in your Magento Connect extension manager.

## Development and Contribution

 * [Changelog and Download Previous Versions](https://github.com/QubitProducts/UniversalVariable-Magento-Extension/blob/master/CHANGELOG.md)
 * How to start development (Coming Soon)

## Happy Contributors

Thank you to the contributors improving our code base:

* Robert Coleman [@rjocoleman](https://github.com/rjocoleman)
* [@rgranadino](https://github.com/rgranadino)

## License

The extension is released under Apache License 2.0
