# Barnraiser-Roundhouse

## About
This project was published on http://barnraiser.org/ a while ago. Although it has very promising features it was frozen and finally dropped for reasons that are explained [here](http://barnraiser.org/signing_off). According to this the main reason was the rise of similar products by companies with sufficient economic power to attract more users than a small enthusiastic bunch of coders.

Today we know where that evolution led us and there's a tendency to move away from data-miners back to software under personal control. Funny enough this was exactly the intention of all projects at http://barnraiser.org/. We could say they were just a bit too far ahead of their time.

Fortunately the latest versions of the projects are still available on their website. In order to preserve the code and hopefully help to adjust it to modern requirements I took the freedom to import them here. 

I'm not an experienced programmer, so there's not so much activity to be expected from my side. Much more this repository should be considerd as a base-camp for real coders who are able and willing to spend some time on these projects. I don't mind if they fork and develop on their own or ask for write-access to this repo. Although in the first case I would appreciate pull requests in order to keep the things together.

The following is the original description of *Roundhouse*, taken from its homepage http://barnraiser.org/roundhouse
There's also a very comprehensive user's manual available at http://barnraiser.org/roundhouse_guide which I copied
[here](documents/roundhouse_guide.html) for backup purposes.

## Introducing Roundhouse
Import blog entries from the whole blogsphere into your blog stream with Roundhouse; our social blogging tool.

Roundhouse is a lightweight easy to learn and use blogging tool. You can install it for just yourself or for many people to each have their own blog.

Roundhouse was built as an experiment to see if we can incorporate a blogroll into the main blog feed in a way that you could gather blog entries from friends into your blog to create a combination of your blog entries and your favourite blog entries from your friends to create a social blog.

## Features
* Install as a single blog or a service to host many separate blogs.
* OpenID support.
* Import RSS feed items directly into your blog.
* Import Digg items directly into your blog.
* Upload many pictures for inclusion in your blog.
* Import Youtube movies into your blog.
* Blog archive built in.
* Tagcloud built in.
* RSS feed built in.
* Highlights listing.
* Receive comments on blogs.
* Share blog entries (to del.icio.us, Digg, StumbleUpon and Technorati).
* Optimized for Google indexing.
* Themed "skins" which can be easily downloaded and added.
* Multi-lingual.
* Easy to use publishing system.
* Lightweight easy to use interface.
* Free (GPL) software license

## Technical considerations
Roundhouse requires a web server running either Apache 1.3/2.x or IIS5/IIS6 with PHP5.x installed including GD library and Gettext (Curl and BCMath if you want OpenID support).

For multiple instances you will require wildcard sub-domains.
