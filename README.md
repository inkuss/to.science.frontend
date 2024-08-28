# About

to.science.drupal is a collection of Drupal 7 modules that provide a front end for [toscience](https://github.com/hbz/to.science) (repository and
graph-based api for library data).

# Install redland bindings

to.science.drupal depends on the Redland rdf bindings and on curl modules for php5.

Install redland bindings for php56 on SLES.

*Installation on SLES 15 SP6:*

Execute all commands as *root* or with *sudo su*.

Install libraptor2-0 and raptor with YaST2.

    zypper addrepo https://download.opensuse.org/repositories/X11:common:Factory/SLE_15_SP2/X11:common:Factory.repo
    zypper refresh

Then install librasqal3 (RDF Parser Toolkit for Redland), librdf0, rasqal und redland with YaST2.

Install required packages:

    zypper in -t pattern devel_basis

Install libredland-devel, raptor, re2c, gdb, valgrind, swig, libxml2-devel, sqlite3-devel, php56-devel and librasqal-devel with YaST2.

Download Redland bindings, replace Makefile in the subfolder "php":
Execute as user toscience

    cd /opt/toscience
    wget https://download.librdf.org/source/redland-bindings-1.0.17.1.tar.gz
    tar xf redland-bindings-1.0.17.1.tar.gz
    cd redland-bindings-1.0.17.1/
    ./autogen.sh --with-php=php56
    cd php/
     
Replace Makefile by [this file](https://github.com/hbz/to.science.drupal/blob/master/makefile_changed_redland_sles_php56.Makefile).
     
    make
    make install

Finally, install redland.so :

    cp redland.so /usr/lib64/php56/extensions/
    cd /etc/php56/conf.d
    cp tokenizer.ini redland.ini
    vim redland.ini
    # replace tokenizer.so by redland.so
    wq

    sudo service php56-fpm restart

# Install to.science.drupal
## Clone the repository and submodules to Drupal's module directory:

    cd sites/all/modules
    git clone https://github.com/hbz/to.science.drupal.git
    cd to.science.drupal
    git submodule update --init
    
## Download non Drupal-core dependency modules:

    cd sites/all/modules
    curl https://ftp.drupal.org/files/projects/entity-7.x-1.1.tar.gz | tar xz
    curl https://ftp.drupal.org/files/projects/entity_js-7.x-1.0-alpha3.tar.gz | tar xz
    curl https://ftp.drupal.org/files/projects/ctools-7.x-1.3.tar.gz | tar xz

# Install Drupal theme

    cd /opt/toscience/drupal/sites/all/themes
    git clone https://github.com/hbz/edoweb-drupal-theme.git

"Edoweb" is one theme for toscience. Create your own theme.

# Activate Drupal modules
Activate "Edoweb Entities" module (e.g. at <http://localhost/drupal/?q=admin/modules>) and confirm activation of dependency modules. Also activate the modules "Chaos Tools" and "Entity Tokens".

Make sure the "Locale" module has been activated if you need to localize your installation. If you have not yet localized your installation, navigate to <http://localhost/drupal?q=admin/config/regional/translate/import>, choose your language file and the language to import it into.  Clear the cache to make sure all field instance labels are updated.

Finally, set the host, user and password for the API at <http://localhost/?q=edoweb/config/storage>  and <http://localhost/?q=edoweb/config/account>s.  You will also reach these pages via Start Page - Configuration - APIs  or Accounts, respectively.

Activate "Edoweb" theme (e.g. at http://localhost/drupal/?q=admin/appearance).

Log in at http://localhost/user .

Navigate to http://localhost/resource . This will show you the start page of toscience (using the chosen theme).


# Localization

To localize your Drupal installation, first activate the "Localize" module. 

Then download your preffered language from <https://localize.drupal.org/translate/languages/>. 

Navigate to <http://localhost/drupal?q=admin/config/regional/translate/import>, choose your language file and the language to import it into. 

To localize to.science.drupal (German file available [here](german.po), proceed as describe above. Clear the cache to make sure all field instance labels are updated.
