# Security Policy

## Version Support Status

Status | Major Version
---|---
EoL/Dead/Unsupported | &lt;v2
Stable/Mature/Supported | v2
Unstable/Alpha/Beta/Planning | n/a

## Reporting a Vulnerability

If you've discovered a new vulnerability, please firstly attempt to reasonably confirm which versions of the package the newly discovered vulnerability affects.

If it affects only versions on branches that've already reached "EoL/Dead" status (i.e., doesn't affect anything on any currently maintained branches, tagged or otherwise), it most likely won't ever be fixed, and the best course of action would be to report all pertinent details directly to the issues page at this repository. I can then document the vulnerability and encourage any affected users to update to a non-affected version as soon as possible.

If it affects any versions on any currently maintained branches (tagged or otherwise, and including any active dev code), I'll do what I can to fix the problem as soon as possible (assuming that it hasn't already been fixed since the latest available versions at the affected branches).

If the vulnerability has already been fixed, is already public knowledge, or has otherwise already been publicly disclosed somehow by the project somewhere (e.g., in the changelogs, at the issues page, etc), the best course of action would be to report all pertinent details directly to the issues page at this repository. If an issue concerning the vulnerability already exists, I would ask that you append your report to that issue, rather than creating additional issues (to avoid duplication and clutter at the issues page). If no such issue exists yet, I welcome you to create one.

However, if the vulnerability hasn't been fixed yet, isn't yet public knowledge, and hasn't yet been publicly disclosed by the project anywhere (i.e., if you've found a "zero-day"), I would ask then that you keep user safety in mind and to report your findings in a responsible, conscientious manner. Public disclosure of previously unknown vulnerabilities and "zero-days", when affecting currently supported versions, could directly put users at risk from those that may wish to do them, their websites, or their data harm. (This, of course, refers only to vulnerabilities; Bugs or faults in the codebase that wouldn't generally be regarded as vulnerabilities could still be posted directly to the issues page).

In cases where reporting to the issues page or to publicly accessible channels would pose such risks to users, or where private communication may be required for any particular reason, you're welcome to contact [me](https://github.com/Maikuolan) by other means (such as email or [private messaging](https://www.facebook.com/caleb.mazalevskis)).

## Currently Known Vulnerabilities

### TLS certificate verification vulnerability.
- **Description:** The request handler disabled TLS certificate verification by default for HTTPS and FTPS requests (both via curl and via the streams fallback), allowing anyone able to intercept a request to present a self-signed certificate and read or modify the data exchanged. Certificate verification is now enabled by default; it can still be disabled for specific channels via the CURLOPT_SSL_VERIFYPEER override (e.g., at hosts without any CA bundle available).
- **GitHub Security Advisory:** [GHSA-qmgw-f4f6-3x6j](https://github.com/Maikuolan/Common/security/advisories/GHSA-qmgw-f4f6-3x6j)
- **CVE:** *Pending.*
- **Affected versions:** Common Classes Package &lt;= 2.16.1
- **Solution:** Update Common Classes Package to v2.16.2 or later. Alternatively, if updating isn't possible, the implementation could set an override to specify `CURLOPT_SSL_VERIFYPEER` as `true`. Note that this vulnerability affects only the request handler (implementations which implement the package to make use of classes other than request handler but not the request handler itself aren't at risk, though updating is still recommended regardless).
