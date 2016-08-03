module.exports = function (grunt) {

	var package = grunt.file.readJSON('package.json');

	// Project configuration.
	grunt.initConfig({
		pkg: package,
		// Bump version numbers
		version: {
			pkg: {
				src: ['package.json', 'composer.json'],
			}
		},
		gitcommit: {
			release: {
				options: {
					message: 'chore(build): release <%= pkg.version %>',
				},
				files: {
					src: ["composer.json", "package.json", "CHANGELOG.md"],
				}
			},
		},
		gitfetch: {
			release: {
				all: true
			}
		},
		gittag: {
			release: {
				options: {
					tag: '<%= pkg.version %>',
					message: 'Release <%= pkg.version %>'
				}
			}
		},
		gitpush: {
			release: {
			},
			release_tags: {
				options: {
					tags: true
				}
			}
		},
		conventionalChangelog: {
			options: {
				changelogOpts: {
					preset: 'angular'
				}
			},
			release: {
				src: 'CHANGELOG.md'
			}

		}
	});

	grunt.loadNpmTasks('grunt-version');
	grunt.loadNpmTasks('grunt-conventional-changelog');
	grunt.loadNpmTasks('grunt-git');

	grunt.registerTask('readpkg', 'Read in the package.json file', function () {
		grunt.config.set('pkg', grunt.file.readJSON('package.json'));
	});

	grunt.registerTask('release', function (n) {
		var n = n || 'patch';
		grunt.task.run([
			'version::' + n,
			'readpkg',
			'conventionalChangelog:release',
			'gitfetch:release',
			'gitcommit:release',
			'gittag:release',
			'gitpush:release',
			'gitpush:release_tags'
		]);
	});
};
