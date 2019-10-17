# Stiff-competition
A repo to keep track of how a bunch of PHP CMS'es are doing. And some projects not built in PHP. And also some things that might not be completely CMS'es. 

## Setup

First, configure the DB credentials, in `.env`

Then, run 

```bash 
bin/console doctrine:database:create
bin/console doctrine:schema:create
```

Update: 

```
bin/console app:github
```
