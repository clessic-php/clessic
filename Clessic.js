class Clessic{
	constructor(requestFilter = null, responseFilter = null){
		Object.assign(this, {requestFilter, responseFilter});
	}
	static parse(cmdStr){
		const tokens = [];
		let esc = false;
		let pre = null;
		let add = true;
		let n = -1;
		for(let ch of cmdStr){
			let v = null;
			if(esc){
				if(ch == "\""){
					esc = false;
				}else{
					v = ch;
				}
				pre = ch;
			}else if(pre == "^"){
				v = ch;
				pre = null;
			}else{
				if(ch == "\""){
					esc = true;
					if(pre == "\""){
						v = ch;
					}
				}else if(ch == "^"){
				}else if(ch == " "){
					add = true;
				}else{
					v = ch;
				}
				pre = ch;
			}
			if(v == null){
				continue;
			}
			if(add){
				n = tokens.length;
				tokens.push(v);
				add = false;
			}else{
				tokens[n] += v;
			}
		}
		return tokens;
	}
	static encode(cmdArgs){
		return cmdArgs.map(encodeURIComponent).join("+");
	}
	static navigate(cmdArgs){
		location.href = new URL(`?${this.encode(cmdArgs)}`, location.href).href;
	}
	static replace(cmdArgs){
		location.replace(new URL(`?${this.encode(cmdArgs)}`, location.href).href);
	}
	run(cmdArgs, options = null){
		const cmd = new URL(`?${this.constructor.encode(cmdArgs)}`, location.href).href;
		const request = (options == null) ? new Request(cmd) : new Request(cmd, options);
		if(this.requestFilter != null){
			this.requestFilter(request);
		}
		return fetch(request).then(response => {
			if(this.responseFilter != null){
				const resolve = this.responseFilter(response);
				if(resolve !== void(0)){
					return resolve;
				}
			}
			const type = response.headers.get("Content-Type");
			if(type.includes("text/")){
				return response.text();
			}
			if(type.includes("application/json")){
				return response.json();
			}
			return response.arrayBuffer();
		});
	}
}