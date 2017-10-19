# <img src="https://github-sect.s3-ap-northeast-1.amazonaws.com/logo.svg" width="28" height="auto"> WP Instagram JSON
[![Build Status](https://travis-ci.org/sectsect/wp-instagram-json.svg?branch=master)](https://travis-ci.org/sectsect/wp-instagram-json) [![PHP-Eye](https://php-eye.com/badge/sectsect/wp-instagram-json/tested.svg?style=flat)](https://php-eye.com/package/sectsect/wp-instagram-json) [![Latest Stable Version](https://poser.pugx.org/sectsect/wp-instagram-json/v/stable)](https://packagist.org/packages/sectsect/wp-instagram-json)  [![License](https://poser.pugx.org/sectsect/wp-instagram-json/license)](https://packagist.org/packages/sectsect/wp-instagram-json)

### Generate JSON file with object data returned from Instagram API (for Sandbox Mode).

## Why Sandbox Mode?

### Understanding the Instagram API in three minutes
In order to work with the Instagram API, sooner or later, you must find your way through the nebulous API client registration and authorization process. Understanding the API access limitations can prevent a lot of wasted time, because they often result in unexpected data rather than straight-forward authentication errors that are easier to diagnose.

### The infamous June 2016 API restrictions
On June 1 2016, console errors lit up all over the world when Instagram significantly restricted access to its API. The first thing to understand is that it was a deliberate business decision by Instagram, designed to prevent their API from being used for a variety of purposes. Understanding this fact will not fix the errors in the console, but it makes the restrictions more intuitive to work around if you understand their intent.

### Sandbox mode vs "live" mode
The gatekeeper between developers and full API access is called sandbox mode. The documentation presents it as a temporary step in the development process, but the overwhelming majority of projects will never leave sandbox mode because Instagram only grants full access to their API for a handful of very specific use cases:
- “My app allows people to login with Instagram and share their own content”
- “My product helps brands and advertisers understand, manage their audience and media rights.”
- “My product helps broadcasters and publishers discover content, get digital rights to media, and share media with proper attribution.”

If you cannot convince the Instagram lords that your app serves one of these specific purposes, it will be rejected if you submit it to “Go live”.  
For this reason, it may be more intuitive to think of it as “sideline mode”.

### What can you do while in sandbox mode?
- Apps in sandbox are restricted to 10 users
- Data is restricted to the 10 users and the 20 most recent media from each of those users
- Reduced API [rate limits](https://www.instagram.com/developer/limits/) `500/hour`

via @ https://www.instagram.com/developer/sandbox/


So I have developed this Plugin to generate JSON files at scheduled times for the data returned from Instagram API for those conditions. This is also for performance optimization.


## Requirements

- PHP 5.4+

## Installation

1. Clone this Repo into your `wp-content/plugins` directory.
  ```sh
  $ cd /path-to-your/wp-content/plugins/
  $ git clone git@github.com:sectsect/wp-instagram-json.git
  ```

2. Activate the plugin through the 'Plugins' menu in WordPress.<br>

3. Go to `Instagram` on your wordpress admin panel.

4. Set the following values and save it once.
  - Cache Expire `(min)`
  - Count `(Range: 1-20)`
  - Account Name
  - Access Token :warning: You need get Instagram API Access Token  in advance.

That's it:ok_hand:  
A file will be generated at the time of the first web access into the following location.  
```
/wp-content/plugins/wp-instagram-json/json/instagram.json
```

Now, you can get the URL with javascript variable `wp_ig_json.json_url`.

## Saved Object Structure
```json
{  
  "pagination":[  

  ],
  "data":[  
    {  
      "id":"",
      "user":{  
        "id":"",
        "full_name":"",
        "profile_picture":"",
        "username":""
      },
      "images":{  
        "thumbnail":{  
          "width":150,
          "height":150,
          "url":""
        },
        "low_resolution":{  
          "width":320,
          "height":320,
          "url":""
        },
        "standard_resolution":{  
          "width":640,
          "height":640,
          "url":""
        }
      },
      "created_time":"",
      "caption":{  
        "id":"",
        "text":"",
        "created_time":"",
        "from":{  
          "id":"",
          "full_name":"",
          "profile_picture":"",
          "username":""
        }
      },
      "user_has_liked":,
      "likes":{  
        "count":
      },
      "tags":[  

      ],
      "filter":"",
      "comments":{  
        "count":
      },
      "type":"",
      "link":"",
      "location":,
      "attribution":,
      "users_in_photo":[  

      ],
      "carousel_media":[  
        {  
          "images":{  
            "thumbnail":{  
              "width":150,
              "height":150,
              "url":""
            },
            "low_resolution":{  
              "width":320,
              "height":320,
              "url":""
            },
            "standard_resolution":{  
              "width":640,
              "height":640,
              "url":""
            }
          },
          "users_in_photo":[  

          ],
          "type":""
        },
        {  
          "images":{  
            "thumbnail":{  
              "width":150,
              "height":150,
              "url":""
            },
            "low_resolution":{  
              "width":320,
              "height":320,
              "url":""
            },
            "standard_resolution":{  
              "width":640,
              "height":640,
              "url":""
            }
          },
          "users_in_photo":[  

          ],
          "type":"image"
        }
      ]
    },
	{
	  "id":"",
	  ...
    }
  ],
  "meta":{  
    "code":200
  },
  "file_generate_datetime":"2019\/12\/31 00:00:00"
}
```


## NOTES for Developer

- This plugin does not do anything on the Front-end. Just generate a JSON file.  
You can access the generated json file in any way you like and output it to the Front-end.  
See [Usage Example](#usage-example).

- WordPress built-in cron has some problems with the specific environment.   
In order to avoid those risks, this Plugin uses periodic processing for WordPress [Transient](https://codex.wordpress.org/Transients_API) instead of `WP-Cron`.

## Usage Example

### Ajax with jQuery

```html
<div id="app">
  <ul id="instagram"></ul>
</div>
```

```javascript
jQuery(function() {
  jQuery.ajax({
    url       : wp_ig_json.json_url,
    type      : "GET",
    dataType  : 'json',
    beforeSend: function() {}
  }).done(function(res) {
    if (res && res.data) {
      let list = '';
      jQuery.each(res.data, function(i, photo) {
        let html = '<li data-id="' + photo.id + '">';
        html += '<a href="' + photo.link + '" target="_blank">';
        html += '<img src="' + photo.images.standard_resolution.url + '" />';
        html += '</a>';
        html += '</li>';
        list += html;
      });
      jQuery('#instagram').html(list);
    }
  }).fail(function() {
    alert("Load Error. Please Reload...");
  }).always(function(res) {
    jQuery('#instagram').addClass('ready');
  });
});
```

### Ajax with Vue.js

```php
// functions.php
wp_enqueue_script('vue', 'https://unpkg.com/vue@2.5.2/dist/vue.min.js', array());
wp_enqueue_script('axios', 'https://unpkg.com/axios@0.16.2/dist/axios.min.js', array('vue'));
wp_enqueue_script('app', get_template_directory_uri() . '/assets/js/app.js', array('axios'), null, true);
```

```html
<div id="app" v-cloak>
  <ul id="instagram">
    <li v-for="photo in photos" v-bind:data-id="photo.id">
      <a v-bind:href="photo.link" target="_blank">
        <img v-bind:src="photo.images.standard_resolution.url" />
      </a>
    </li>
  </ul>
</div>
```

```javascript
const app = new Vue({
  el: '#app',
  data: {
    photos: [],
    errors: []
  },
  created() {
    axios.get(wp_ig_json.json_url)
    .then(response => {
      if (response.status == '200') {
        this.photos = response.data.data
      }
    })
    .catch(e => {
      this.errors.push(e)
    })
  },
  updated: function() {
    jQuery('#instagram').addClass('ready');
  }
})
```

## Change log  

See [CHANGELOG](https://github.com/sectsect/wp-instagram-json/blob/master/CHANGELOG.md) file.

## Contributing

1. Create an issue and describe your idea
2. [Fork it](https://github.com/sectsect/wp-instagram-json/fork)
3. Create your feature branch (`git checkout -b my-new-feature`)
4. Commit your changes (`git commit -am 'Add some feature'`)
5. Publish the branch (`git push origin my-new-feature`)
6. Create a new Pull Request
7. Profit! :white_check_mark:

## License

See [LICENSE](https://github.com/sectsect/wp-instagram-json/blob/master/LICENSE) file.