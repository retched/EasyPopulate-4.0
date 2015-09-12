
#EP4Bookx an -EasyPopulate 4.0 fork 
to import Bookx fields by CSV - tested with Zencart 1.5.4

 * @version  0.9.0 - **Still in development, make your changes in a local environment**
 * @see Bookx module for ZenCart
 * Contribution by: @mesnitu

### Note : Still under heavy tests 
>This are some initial files, that already import BookX fields, but isn't yet suitable in a production enviroment. A lot of testings and ideias going on.
It stills **only supports the default language**, and the ability to assing multiple authors and genres are on todo list, but not yet implemented.


### Quick review : 
[Book X](https://sourceforge.net/p/zencartbookx) it's an impressive ZenCart mod made by @philou that introduces a new product type for books to the Zen Cart shop system. 

**EP4Bookx** is a fork of  Easy Populate 4.0 to support Bookx fields.
This is an attempt to give a book shop a quick start to get up and running, but also,to make changes whe it comes to a large number of books. 
In sum, use the power of Easy Populate 4.0 for BookX. 

For now, this are the supported fields (just the names, no description yet)
#### Supported Fields
* bookx_author_name 
* bookx_author_type
* bookx_subtitle    
* bookx_genre_name
* bookx_publisher_name     
* bookx_series_name       
* bookx_imprint
* bookx_binding
* bookx_printing
* bookx_condition
* bookx_isbn
* bookx_size
* bookx_volume
* bookx_pages
* bookx_publishing_date     

### Installation

* Install EasyPopulate as you would. 
* Enable Bookx fields in EP configuration page.
* You can assign a fallback genre, or leave it blank (default: General) 
 


### Use it (still in idea stage)

In EP4 configuration page, you may give a default genre. I've done so, cause in my personal use, there are a lot of books that actually goes into a general genre, so I don't have to wright then down, but mainly, because if genre or author is empty, it wont be on BookX filter. So just this is a just in case measure. 

>Note: At this stage, possibly other fallback fields, such as authors could be add it, or completely remove this functionality.

If you already know EP4, the procedures are the same. You'll download the **Complete Products (with Metatags)**

Make your changes, and reupload them. 

At upload, if empty fields are found, such as authors, publishers, etc... a report is generated (still working on it ), with edit link to the the book.
I found that there's no need to skip fields, but instead, insert them, and have the possibility to have a more visual way to know witch fields are missing and were.
####Removing Books with EPBookx
EP4Bookx uses status 10 (same as 9 in EP4), but changed to remove bookx fields associated with the products.
So you would do v_status 10, to remove books.


 ## Todo  
 - [ ]  export /import with support for languages
 - [ ]  export /import assinged multiple authors
 - [ ]  export /import assigned multiple genres
 - [ ]  Se
 