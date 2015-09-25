# NSPM - Network Security & Policy Manager #

This project was initially designed to provide an online interface to the Linux Netfilter/Iptables firewall, and this without prior knowledge of the related CLI commands and options. Different interfaces already exist but most of them require a fresh install of a specific Linux distribution such as the well-known IPCop, need an existing Webmin-like core component, or must be run under X environment. For those who want to keep their own Linux flavor, already have a panel of services running on their gateway, or need to easily and remotely administrate their firewall through a standard web browser, this application should fit their needs.

## Technical overview ##
  * Fully compatible with your current Linux distribution
  * No installation nor exotic package required
  * Direct integration with Linux kernel and binaries
  * No Iptables commands knowledge required
  * Full-featured PHP5 and templates-oriented web application
  * No database, hooks or external scripts needed
  * Standards compliant and accessibility-oriented interface

## Features overview ##
  * System health, network interfaces and services status display
  * Monitor and block current incoming and outgoing connections
  * Manage default firewall policies by chain and table
  * View, create, edit, duplicate and reorder filter and NAT rules
  * Disable rules when not needed and re-enable them later
  * Automatic composition of advanced filtering options
  * Fully compatible with classic Iptables CLI use
  * Transactional configuration with session commit and rollback

_For more information, documentation or screenshots please take a look at NSPM project home page :_ http://www.netspm.org