var lfs=new LocalFileSystemSync();
var fs = requestFileSystemSync(TEMPORARY, 1024*1024 /*1MB*/);
self.requestFileSystemSync = self.webkitRequestFileSystemSync || self.requestFileSystemSync;