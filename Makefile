
VIEWS = \
	layout/default \
	admin/assign_edit \
	admin/assign_main \
	admin/assignment_edit \
	admin/assignment_list \
	admin/course_edit \
	admin/course_list \
	admin/download_main \
	admin/download_whole_course \
	admin/enroll_edit \
	admin/enroll_main \
	admin/grade_main \
	admin/grade_whole_course \
	admin/grade_whole_course_print \
	admin/main \
	admin/user_edit \
	admin/user_list \
	admin/user_reset_password \
	assignment/index \
	course/index \
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

nevilis.pot: | $(VIEWS_OUT_FILES)
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

locale/%/LC_MESSAGES/nevilis.po: nevilis.pot
	msgmerge $@ nevilis.pot --output-file=$*.new.po
	if [ -z "`diff -ud $@ $*.new.po | tail -n +3 | grep '^[-+]' | grep -v 'POT-Creation-Date'`" ]; then \
		rm $*.new.po; \
	else \
		mv $*.new.po $@; \
	fi

clean:
	rm -f $(VIEWS_OUT_FILES) $(MO_FILES) nevilis.pot
