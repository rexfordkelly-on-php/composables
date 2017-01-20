#################################################################### 
############ 				X rewrites 				############
#################################################################### 

RewriteEngine On
RewriteBase /

## Prevent access to any directories prefixed with "-"

	RewriteCond %{SCRIPT_FILENAME} -d [OR]
	RewriteCond %{SCRIPT_FILENAME} -f
	RewriteRule "(^|/)-" - [R=404,L]

## Prevent Direct Access to any directories prefixed with "_"

	RewriteCond %{THE_REQUEST} /_ [NC]
	RewriteRule ^ - [R=404,L]

## Force Trailing Slashes

	RewriteCond %{REQUEST_URI} /+[^\.]+$
	RewriteRule ^(.+[^/])$ %{REQUEST_URI}/ [R=301,L]

## Sink the _apps directory, so it behaves as if it was in the document root

	RewriteCond %{REQUEST_URI} !^/_apps/
	RewriteCond %{REQUEST_FILENAME} !-f
	RewriteCond %{REQUEST_FILENAME} !-d
	RewriteRule (.*) _apps/$1 [L]