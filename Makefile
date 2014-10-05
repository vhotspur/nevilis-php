
VIEWS = \
	layout/default \
	login \
	main \
	password

LANGAUGES = \
	cs_CZ

PHP_CLI = php

VIEWS_OUT_FILES = $(addprefix views/, $(addsuffix .html.php,$(VIEWS)))
PO_FILES = $(addprefix locale/,$(addsuffix /LC_MESSAGES/nevilis.po,$(LANGAUGES)))
MO_FILES = $(addprefix locale/,$(addsuffix /LC_MESSAGES/nevilis.mo,$(LANGAUGES)))

all: $(VIEWS_OUT_FILES) $(MO_FILES)

%.html.php: %.tpl.html.php
	$(PHP_CLI) ./tools/make_views.php <$< >$@

nevilis.po: | $(VIEWS_OUT_FILES)
	find -name '*.php' -and -not -name '*.tpl.*' \
		| xargs xgettext \
			-dnevilis \
			--package-name="Nevilis PHP" \
			--package-version=0.1 \
			-o - \
		| sed 's@\(Content-Type:.*charset=\)CHARSET\(.*\)@\1UTF-8\2@' \
		> $@

update-po-files: $(PO_FILES)

%.mo: %.po
	msgfmt $< -o $@

locale/%/LC_MESSAGES/nevilis.po: nevilis.po
	mv $@ $*.old.po
	msgmerge $*.old.po nevilis.po --output-file=$@
	rm $*.old.po

clean:
	rm -f $(VIEWS_OUT_FILES) $(MO_FILES) nevilis.po
