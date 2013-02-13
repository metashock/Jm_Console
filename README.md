# J@m; Console

..

## Installation

To install Jm_Autoloader you can use the PEAR installer or get a tarball and install the files manually.

___
### Using the PEAR installer

If you haven't discovered my pear channel yet you'll have to do it. Also you should issue a channel update:

    pear channel-discover metashock.de/pear
    pear channel-update metashock

After this you can install Jm_Autoloader. Note that if you installed one of the other Jm packages before it is likely that you have already installed Jm_Autoloader as its a dependency of the most jAm packages. The following command will install the lastest stable version:

    pear install -a metashock/Jm_Console

If you want to install a specific version or a beta version you'll have to specify this version on the command line. For example:

    pear install -a metashock/Jm_Console-0.3.0

___
### Manually download and install files

Alternatively, you can just download the package from http://www.metashock.de/pear and put into a folder listed in your include_path. Please refer to the php.net documentation of the include_path directive.


## Documentation

### API documentation

The API docs can be found here:

    http://metashock.de/docs/api/Jm/Console/index.html



