# stiff-competition
A repo to keep track of how a bunch of PHP CMS'es are doing

## Setup

First, configure the DB credentials, in `.env`

Then, run 

```bash 
bin/console doctrine:database:create
bin/console doctrine:schema:create

```