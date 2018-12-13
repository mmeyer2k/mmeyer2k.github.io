---
layout: post
title: Shallow clone your repo to speed up circle-ci builds
tags: [github, bash, circle-ci]
---

# Find out how a developer decreased CI build time by 20%

Thankfully this post isn't click bait...

When your repository history size becomes very large it can stress your CI environment. 
Shallow clones will reduce the total amount of data transferred by skipping the history and history downloading the file objects.
Our checkout steps went from 50 seconds down to around 6 seconds on circle-ci.

Open your `.circleci/config.yml` file and replace your standard `-checkout` step with this new procedure.

```bash
    #-checkout
    - run:
        name: Checkout code
        command: |-
            # add github.com to known hosts
            mkdir -p ~/.ssh
            echo 'github.com ssh-rsa AAAAB3NzaC1yc2EAAAABIwAAAQEAq2A7hRGmdnm9tUDbO9IDSwBK6TbQa+PXYPCPy6rbTrTtw7PHkccKrpp0yVhp5HdEIcKr6pLlVDBfOLX9QUsyCOV0wzfjIJNlGEYsdlLJizHhbn2mUjvSAHQqZETYP81eFzLQNnPHt4EVVUh7VfDESU84KezmD5QlWpXLmvU31/yMf+Se8xhHTvKSCZIFImWwoG6mbUoWf9nzpIoaSjB+weqqUUmpaaasXVal72J+UX2B+2RPW3RcT0eOzQgqlJL3RKrTJvdsjE3JEAvGq3lGHSZXy28G3skua2SmVi/w4yCE6gbODqnTWlg7+wC604ydGXA8VJiS5ap43JXiUFFAaQ==
            ' >> ~/.ssh/known_hosts

            # add the user ssh key and set correct perms
            umask 077
            echo "$CHECKOUT_KEY" > ~/.ssh/id_rsa
            chmod 0600 ~/.ssh/id_rsa

            # use git+ssh instead of https
            git config --global url."ssh://git@github.com".insteadOf "https://github.com" || true
            git config --global gc.auto 0 || true

            # get shallow clone of single repo branch for speed purposes
            git clone --depth=1 --single-branch -b $CIRCLE_BRANCH git@github.com:$CIRCLE_USERNAME/$CIRCLE_PROJECT_REPONAME "/$CIRCLE_PROJECT_REPONAME"
```