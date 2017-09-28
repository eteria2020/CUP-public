<?php 
define('URL', (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on" ? "https://" : "http://").$_SERVER['HTTP_HOST']);
header('Content-Type: application/json'); 
?>
{
  "status": true,
  "data": [
    {
      "tid": "5",
      "name": "Milano",
      "media": {
        "images": {
          "icon": {
            "uri": "<?php echo URL ?>/sites/default/files/assets/images/icona_trasparente-milano.png"
          },
          "icon_svg": {
            "uri": "<?php echo URL ?>/sites/default/files/assets/images/icona_trasparente-milano.svg"
          }
        }
      },
      "informations": {
        "address": {
          "lat": "45.464145",
          "lng": "9.190245"
        }
      }
    },
    {
      "tid": "8",
      "name": "Modena",
      "media": {
        "images": {
          "icon": {
            "uri": "<?php echo URL ?>/sites/default/files/assets/images/icona_trasparente-modena.png"
          },
          "icon_svg": {
            "uri": "<?php echo URL ?>/sites/default/files/assets/images/icona_trasparente-modena.svg"
          }
        }
      },
      "informations": {
        "address": {
          "lat": "44.646429",
          "lng": "10.925301"
        }
      }
    },
    {
      "tid": "7",
      "name": "Roma",
      "media": {
        "images": {
          "icon": {
            "uri": "<?php echo URL ?>/sites/default/files/assets/images/icona_trasparente-roma.png"
          },
          "icon_svg": {
            "uri": "<?php echo URL ?>/sites/default/files/assets/images/icona_trasparente-roma.svg"
          }
        }
      },
      "informations": {
        "address": {
          "lat": "41.899508",
          "lng": "12.486770"
        }
      }
    },
    {
      "tid": "6",
      "name": "Firenze",
      "media": {
        "images": {
          "icon": {
            "uri": "<?php echo URL ?>/sites/default/files/assets/images/icona_trasparente-firenze.png"
          },
          "icon_svg": {
            "uri": "<?php echo URL ?>/sites/default/files/assets/images/icona_trasparente-firenze.svg"
          }
        }
      },
      "informations": {
        "address": {
          "lat": "43.772875",
          "lng": "11.257487"
        }
      }
    }
  ]
}