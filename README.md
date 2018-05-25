# gsow-stats

## Running

First build:

`docker build -t gsow .`

To login:

`docker run -it gsow`

## Project Goals

* Fix unicode issues
* Define a metric measuring the significance of a change (e.g. include missing hyperlink vs. completely new article written)
* Track the number of page views for edited page of GSoW editors
* Provide exploratory and visual tools to assess and measure the impact of the team's efforts

## Code Overview

Web application files live in `www/`

Backend data retrieval is done by `get-data2.py`
