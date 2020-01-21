var dateRemove = new Date();

print("Now: " + dateRemove.toISOString());
dateRemove.setMonth(dateRemove.getMonth() - 12);    // Remove row older that 12 months
print("Remove data older that: " + dateRemove.toISOString());

conn = new Mongo();
db = conn.getDB("sharengo");

print("Clear crash_reports collection\n");
db.crash_reports.remove({"REPORT_DATETIME": { $lt: ISODate(dateRemove.toISOString())}});
db.runCommand({compact: 'crash_reports' });

print("Clear events collection");
db.events.remove({"server_time": { $lt: ISODate(dateRemove.toISOString())}});
db.runCommand({compact:'events'});

print("Clear logs collection");
db.logs.remove({"log_time": { $lt: ISODate(dateRemove.toISOString())}});
db.runCommand({compact: 'logs' });