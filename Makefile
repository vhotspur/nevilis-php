
VIEWS = \
	layout/default \
	login \
	main \
	password

PHP_CLI = php

VIEWS_OUT_FILES = $(addprefix views/, $(addsuffix .html.php,$(VIEWS)))

all: $(VIEWS_OUT_FILES)

%.html.php: %.tpl.html.php
	$(PHP_CLI) ./tools/make_views.php <$< >$@

clean:
	rm -f $(VIEWS_OUT_FILES)
	