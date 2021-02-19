package cn.tqxxkj.wxapp;

import java.io.File;
import java.io.FileInputStream;
import java.io.FileWriter;
import java.io.IOException;
import java.io.Writer;
import java.util.HashMap;
import java.util.Map;
import java.util.Map.Entry;
import java.util.Properties;
import java.util.Set;

import freemarker.template.Configuration;
import freemarker.template.Template;

public class WorkWXCG {

	public static void main(String[] args) throws Exception {
		Properties properties = new Properties();
		properties.load(WorkWXCG.class.getResourceAsStream("/config.properties"));
		CodeGenerator codeGenerator = new CodeGenerator(properties);
		codeGenerator.process(new File("D:\\app\\"));
	}

}
