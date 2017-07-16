# A test-bed for a new way of image manipulation in Neos

See this https://discuss.neos.io/t/rfc-media-imagine-and-images/2299
to understand what this is about.

### This is not meant for production usage!

I want this as a quick way to test the code in the beginning before
splitting this up into official Neos packages.

## Installation

Add this repository to your global `composer.json` by inserting:

```
    "repositories": [
            {
                "type": "vcs",
                "url":  "https://github.com/kitsunet/image-manipulation.git"
            }
        ]
```

and then require the package in your global `composer.json`:

```
    "require": {
        "kitsunet/image-manipulation": "@dev",
    }
```

Note that the package is only compatible with Neos 3.0 and higher.
 
If everything went well Neos should do image manipulation just
like it did before but everything will be grayscale to indicate that
the result came from this package.
