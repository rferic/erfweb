const passwordIsStrongRule = {
	getMessage(field, params, data) {
      	return 'Password is incorrect (require minimum a letter, require minimum a number, require minimum a single character present in the list below)';
  	},
	validate ( value ) {
		return /^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\X])(?=.*[!$#%]).*$/.test(value)
	}
}

export default passwordIsStrongRule