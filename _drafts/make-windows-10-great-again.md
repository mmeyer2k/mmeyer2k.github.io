---
layout: post
title: Make Windows 10 great again
tags: [windows]
---

How to hackerize windows 10...
This post is mostly for me to have a place to catalog my machine configuration steps, but you might find it useful too.

#### Neuter windows update

I have a media server that I never want to restart EVER!
Why microsoft made Windows restart with no prompting I will never understand.

```
%windir%\System32\drivers\etc\hosts
```

```
127.0.0.1 *.download.windowsupdate.com
127.0.0.1 *.microsoft.com
127.0.0.1 *.update.microsoft.com
127.0.0.1 *.windowsupdate.com
127.0.0.1 *.windowsupdate.microsoft.com
127.0.0.1 download.microsoft.com
127.0.0.1 download.windowsupdate.com
127.0.0.1 ntservicepack.microsoft.com
127.0.0.1 test.stats.update.microsoft.com
127.0.0.1 windowsupdate.microsoft.com
127.0.0.1 wustat.windows.com
```

Disable the 'Reboot' orchestrator by renaming the file in this dir:

```
%windir%\System32\Tasks\Microsoft\Windows\UpdateOrchestrator
```

Press `S` to spit on it's grave.

_NOTE:_ you may need to reactivate this to install drivers automatically

---

### Disable gunk

### Disable swap

### Disable hibernate

### Disable restore points