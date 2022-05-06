## GIT FLOW / ENVIRONMENTS / Documentation

### Staging ( origin/staging ) for hotfixes purpose
- Contains the code that is ready to merge on main branch, for new release.
- Hotfixes are merged to staging, which are urgently needed to push to live.
- Environment : [Staging](https://saas.cyberarrowgrcqa.io/)

### Develop ( origin/develop ) for development purpose
- Is up to date with staging, everything fixed on staging is fixed on develop.
- Contains New features development code that are of long run development. 
- Environment : [Develop](https://cyberarrowgrcdev.io/)

### Main ( origin/main ) Stable Release
- Contains latest stable release.
- Release note [Link](https://github.com/cyberarrow-io/grc-multi-tenancy/blob/staging/ReleaseNotes.md)
	
### Project Documentation
- [Link](https://cyberarrow.atlassian.net/wiki/spaces/GRC/pages/1638520/1.+What+is+GRC+and+why+is+it+important)

## Note:
- Everything in staging branch will be on develop but not everything in develop will be on staging.