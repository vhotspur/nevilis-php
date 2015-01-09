
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
	admin/grade_assignment \
	admin/grade_course_main \
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
	password \
	sitedown

LANGAUGES = \
	cs_CZ

INSTALL_DIR = install_dir

PHP_CLI = php
SQLITE = sqlite3

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
			--sort-output \
			-o - \
		| sed 's@\(Content-Type:.*charset=\)CHARSET\(.*\)@\1UTF-8\2@' \
		> $@

update-po-files: $(PO_FILES)

%.mo: %.po
	msgfmt $< -o $@

locale/%/LC_MESSAGES/nevilis.po: nevilis.pot
	msgmerge $@ nevilis.pot --sort-output --output-file=$*.new.po
	if [ -z "`diff -ud $@ $*.new.po | tail -n +3 | grep '^[-+]' | grep -v 'POT-Creation-Date'`" ]; then \
		rm $*.new.po; \
	else \
		mv $*.new.po $@; \
	fi

prep-install:
	! [ -d "$(INSTALL_DIR)" ]
	mkdir -p $(INSTALL_DIR)
	
	cp index.php $(INSTALL_DIR)/index.php
	for i in $(MO_FILES); do install -D $$i $(INSTALL_DIR)/$$i; done
	cp -R css lib controllers $(INSTALL_DIR)/
	find views/ -name '*.html.php' -not -name '*.tpl.html.php' \
		-exec install -D {} $(INSTALL_DIR)/{} \;
	
	install -d $(INSTALL_DIR)/db
	$(SQLITE) $(INSTALL_DIR)/db/main.db <db/schema.sql
	echo "<?php printf(\"INSERT INTO user VALUES('admin','%s','Super User','admin');\\n\", password_hash('nevilis1234', PASSWORD_DEFAULT));" \
		| $(PHP_CLI) | $(SQLITE) $(INSTALL_DIR)/db/main.db
	chmod a+w $(INSTALL_DIR)/db/main.db
	
	install -d -m 0777 $(INSTALL_DIR)/userfiles
	
	sed \
		-e "s@\(option('file_dir',\).*@\1 'userfiles/');@" \
		-e "s@\(option('database',\).*@\1 'sqlite:db/main.db');@" \
		config.php.dist > $(INSTALL_DIR)/config.php

clean:
	rm -f $(VIEWS_OUT_FILES) $(MO_FILES) nevilis.pot
