tesla-php
=========

## Description

Tesla PHP 5+ library for REST API Interaction. Based on: http://docs.timdorr.apiary.io/

You will need to fill in the Client ID and Secret with a valid ID and Secret. Check the apiary docs for more info.


## Usage

### To create a TeslaAPI object

    <?php
    include("TeslaAPI.php");
    $tesla = new TeslaAPI('email@address.com','password');

### Unlock Command

    $tesla->get_door_unlock();

## Contributing

1. Fork it
2. Create your feature branch (`git checkout -b my-new-feature`)
3. Commit your changes (`git commit -am 'Add some feature'`)
4. Push to the branch (`git push origin my-new-feature`)
5. Create new Pull Request
