[![Latest Stable Version](http://poser.pugx.org/itribe/typoscript-code/v)](https://packagist.org/packages/itribe/typoscript-code)
[![Total Downloads](http://poser.pugx.org/itribe/typoscript-code/downloads)](https://packagist.org/packages/itribe/typoscript-code)
[![License](http://poser.pugx.org/itribe/typoscript-code/license)](https://packagist.org/packages/itribe/typoscript-code)

# TypoScript code

TYPO3 extension for inserting any TypoScript code to a page as a normal content element.

## Installation

There are following ways for extension installation.

### 1. Get extension using [PHP Composer](https://getcomposer.org)

`composer require itribe/typoscript-code`

### 2. Get extension from the Extension Manager

Press the "Retrieve/Update" button and search for the extension key "typoscript_code" and import the extension from the repository. Also you can get current version from [TYPO3 Extension Repository](https://typo3.org/extensions/repository/view/typoscript_code>) by downloading either the t3x or zip version. Upload the file afterwards in the Extension Manager.

## Version status

| Version           | TYPO3                                                        |
|-------------------|--------------------------------------------------------------|
| Version **5.x**   | Compatible with TYPO3 6.2.x - 7.6.x                          |
| Version **6.0.x** | Compatible with TYPO3 8.7.x                                  |
| Version **6.1.x** | Compatible with TYPO3 8.7.x - 9.5.x                          |
| Version **6.2.x** | Compatible with [TYPO3 10](https://get.typo3.org/version/10) |
| Version **6.3.x** | Compatible with [TYPO3 11](https://get.typo3.org/version/11) |

## Users manual

Just add this plugin to a page at a place you like and enter some TypoScript code to a text field. Code is considering to be a COA definition, it will be rendered by TYPO3 and inserted to a page.

Code could access definitions from website TypoScript templates, as well as constants.

![Example](/Resources/Public/example.jpg)